FerotresRedsysBundle Documentation
==================================

The FerotresRedsysBundle adds support for manage payments in redsys platform for Symfony4.

**Features include**:

- Multi currency | Language
- Multi shops
- Create Payment
- Create Payment Authorization
- Confirm Payment Authorization
- Cancel Payment Authorization
- Payment response validators
- OpenSSL
- HMAC_SHA256_V1
- Unit tested

**Note:** This bundle does *not* provide entities for manage payments.

[![Build Status](https://travis-ci.org/ferotres/redsys-bundle.svg?branch=master)](https://travis-ci.org/ferotres/redsys-bundle)

Installation
------------

 **Download FerotresRedsysBundle**.

``` bash
$ composer require ferotres/redsys-bundle
```

**Add routing resource**.
``` yaml
# config/routes/ferotres_redsys.yaml
_ferotres_redsys:
    resource: "@FerotresRedsysBundle/Resources/config/routes.xml"
```

**Configure the FerotresRedsysBundle**.

You can configure the bundle for each environment.
``` yaml
# config/packages/ferotres_redsys.yaml
ferotres_redsys:
    url: 'https://sis-t.redsys.es:25443/sis/realizarPago' # this is a test url
    shops:
        sample1:
           merchant_name: 'sample1'
           merchant_code: '45677897'
           success: 'payment-success' # route for payment succes redirection
           error:   'payment-serror'  # route for payment error redirection
           terminals: # One terminalis mandatory
              - { secret: 'sq7HjrUOBfKmC344354fvv0gJ7', ces: true,  num: 1, iso_currency: 'EUR' }
              - { secret: 'sq7Hvfrty676LgskD5srU870g7', ces: false, num: 2, iso_currency: 'EUR' }
              - { secret: 'sq7HjrUOBfKmC576ILyythU870', ces: false, num: 4, iso_currency: 'USD' }
              
        # Is posible add more shops, but almost one is needed.
```

Configuration
-------------

Terminal configuration (All params is mandatory)
- **secret**: is a key provided by redsys.
- **ces**: set to true if terminal is configured for secure payments.
- **num**: Is a terminal number provided by redsys.
- **iso_currency**: ISO Currency code with 3 digits.

**Valid ISO Currencies**

- EUR: Euro
- USD: Dollar
- AUD: Australian Dollar
- GBP: Pound
- JPY: Yen
- CAD: Canada Dollar
- ARS: Peso Argentino
- INR: Rupia
- MXN: Peso Mexicano
- PEN: Sol Peruano
- CHF: Franco suizo
- BRL: Real Brasileño
- VEF: Bolivar Venezolano
- TRY: Lira Turca


Create Payment
-------------
Transaction type **0**

**Create a action method**.
``` php
class PaymentController extends AbstractController
{

    /** @var RedsysRedirection  */
    private $redsysRedirection;

    /**
     * DefaultController constructor.
     * @param RedsysRedirection $redsysRedirection
     */
    public function __construct(RedsysRedirection $redsysRedirection)
    {
        $this->redsysRedirection = $redsysRedirection;
    }
    
    public function pay(int $optionalIdOffer)
    {
        $paymentOrder = PaymentOrderBuilder::create()
            ->toApp('sample1')
            ->withAmount(1000)
            ->withCurrency('EUR')
            ->withLocale('ES')
            ->withPaymentHolder('Juan Alvarez')
            ->withDescription('Product Test description')
            ->withOrder('201901015001')
            // This params is used for success/error callbacks and notification response
            ->addUserParams(['optionalIdOffer' => $optionalIdOffer])
            // For use a secure terminal 
            ->usingCes(true) 
            ->build();
            
         /** @var RedsysOrder $redsysOrder */
         $redsysOrder = $this->redsysRedirection->createPayment($paymentOrder);
         
         return $this->render('Acme/pay.html.twig', ['redsysOrder' => $redsysOrder]);
    }
    
}
```

**Create a template for autosubmited form (This is a sample, the required part is include payment form)**
```
# templates/Acme/pay.html.twig

{% extends "Acme::layout.html.twig" %}

{% block body %}

     {{ include('@FerotresRedsys/pay-form.html.twig') }}
     
{% endblock %}
```

**Manage autosubmit of form in js.**
``` js
# assets/js/app.js
// this submit trigger the redirecton to redsys form for credit card
document.querySelector('#redsys_pay_form').submit();
    
```

Create Payment Authorization
----------------------------
 Is equals to payment only change the method executed. For use this payment method, the terminals must be allow transaction type **O**.
``` php
/** @var RedsysOrder $redsysOrder */
$redsysOrder = $this->redsysRedirection->createAuthorization($paymentOrder);
```

Create Payment Confirmation
----------------------------
Is similar to payment, all params must be the same as authorization. For use this payment method, the terminals must be allow transaction type **P**.
``` php
$paymentOrder = PaymentOrderBuilder::create()
    ->toApp('sample1')
    ->withAmount(1000)
    ->withCurrency('EUR')
    ->withLocale('ES')
    ->withPaymentHolder('Juan Alvarez')
    ->withDescription('Product Test description')
    ->withOrder('201901015001')
    // For this transaction authCode is mandatory
    ->withAuthCode('236055')h
    // This params is used for success/error callbacks and notification response
    ->addUserParams(['optionalIdOffer' => $optionalIdOffer])
    // For use a secure terminal 
    ->usingCes(true) 
    ->build();
// The confirmAuthorization do a curl to redsys, template is not needed.          
/** @var RedsysOrder $redsysOrder */
$redsysOrder = $this->redsysRedirection->confirmAuthorization($paymentOrder);


```

Create Payment Cancelation
----------------------------
 Is similar to confirmation, all params must be the same as authorization. For use this payment method, the terminals must be allow transaction type **Q**.
``` php
// The cancelAuthorization do a curl to redsys, template is not needed.   
/** @var RedsysOrder $redsysOrder */
$redsysOrder = $this->redsysRedirection->cancelAuthorization($paymentOrder);
```

Handle succes and error callbacks
---------------------------------
``` php
class PaymentController extends AbstractController
{
    /**
    * with sample config, this is a payment-success route
    * $optionalIdOffer is passed to paymentOrder with addUserParams
    */
    public function success(int $optionalIdOffer)
    {
        // TODO
    }
    
    /**
     * with sample config, this is a payment-error route
     * $optionalIdOffer is passed to paymentOrder with addUserParams
     */
    public function error(int $optionalIdOffer)
    {
        // TODO
    }
}
```

Handle redsys notification response
-----------------------------------

For handle the notification you can use event listener or a eventSubscriber, in this case i use a event listener.

**Event Litener**
``` php
// src/EventListener/PaymentResponseHandler.php
class PaymentResponseHandler
{
    /**
     * @param RedsysResponseSuccessEvent $redsysResponseEvent
     */
    public function onPaymentSuccess(RedsysResponseSuccessEvent $redsysResponseEvent)
    {
        $redsysResponse = $redsysResponseEvent->redsysResponse();
        
        $order = $redsysResponse->order();
        $authCode = $redsysResponse->authCode();
        // This use __call magic method
        $optionalIdOffer = $redsysResponseEvent->getOptionalIdOffer()
        
        $valid = $redsysResponseEvent->isValidated();
        
        // TODO: Yout bussines logic
        
        // By default, If in this function something throw and exception, RedsysResponseFailedEvent will be triggered.

    }

    /**
     * @param RedsysResponseFailedEvent $responseFailedEvent
     */
    public function onPaymentError(RedsysResponseFailedEvent $responseFailedEvent)
    {
       $redsysResponse = $redsysResponseEvent->redsysResponse();
       
       // This use __call magic method
       $optionalIdOffer = $redsysResponseEvent->getOptionalIdOffer();
       
       $exception = $responseFailedEvent->exception();
       
       $valid = $redsysResponseEvent->isValidated();
       
       // TODO: Yout bussines logic
    }
}
```
**Config event Listener**
``` yaml
# config/services.yaml
app.payment_response_listener:
    class: App\EventListener\PaymentResponseHandler
    tags:
        - { name: kernel.event_listener, event: ferotres_redsys.redsys_response_success, method: onPaymentSuccess }
        - { name: kernel.event_listener, event: ferotres_redsys.redsys_response_failed, method: onPaymentError }
```
License
-------

This bundle is under the MIT license.