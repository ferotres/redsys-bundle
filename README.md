FerotresRedsysBundle
====================

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

Installation
------------

1. **Download FerotresRedsysBundle**.

``` bash
$ composer require ferotres/redsys-bundle
```

2. **Add routing resource**.
``` yaml
# config/routes/ferotres_redsys.yaml
_ferotres_redsys:
    resource: "@FerotresRedsysBundle/Resources/config/routes.xml"
```

3. **Configure the FerotresRedsysBundle**.

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
------------

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


License
-------

This bundle is under the MIT license.