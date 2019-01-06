cs:
	./vendor/bin/php-cs-fixer fix --verbose
.PHONY: cs

cs_dry_run:
	./vendor/bin/php-cs-fixer fix --verbose --dry-run
.PHONY: cs_dry_run

test:
	./vendor/bin/simple-phpunit
.PHONY: test