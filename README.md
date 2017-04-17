# Magento2-SendGrind-TransactionalEmails
Enables sending of transactional e-mails through SendGrid.

## Instalação
Using composer: `composer require gabrielqs/transactional-emails`

## Configuration
Configuration available under Stores -> Configuration -> System -> SMTP

## Adding new Transactional Email Transports
Adding new Transactional Email transports is easy. You can simply create new instances of `Gabrielqs\TransactionalEmails\Api\TransportInterface` under `Gabrielqs\TransactionalEmails\Model\Transports`, implementing the `send()` method.

Afther that, simply add it as an option in the `Gabrielqs\TransactionalEmails\Model\Source\Provider` class.