@account
Feature: Successful changes of login data

    Background:
        Given I am on the page "Account"
    #And print last response

    @password @login
    Scenario Outline: I can change my password
        Given I log in successful as "Max Mustermann" with email "test@example.com" and password "<password>"
        When  I change my password from "<password>" to "<new_password>" with confirmation "<new_password>"
        Then  I should see "Zugangsdaten wurden erfolgreich gespeichert"

        When  I log me out
        And   I follow "Mein Konto"
        And   I log in with email "test@example.com" and password "<password>"
        Then  I should see "Ihre Zugangsdaten konnten keinem Benutzer zugeordnet werden"

    Examples:
        | password  | new_password |
        | shopware  | shopware4    |
        | shopware4 | shopware     |

    @email @login
    Scenario Outline: I can change my email
        Given I log in successful as "Max Mustermann" with email "<email>" and password "shopware"
        When  I change my email with password "shopware" to "<new_email>" with confirmation "<new_email>"
        Then  I should see "Zugangsdaten wurden erfolgreich gespeichert"

        When  I log me out
        And   I follow "Mein Konto"
        And   I log in with email "<email>" and password "shopware"
        Then  I should see "Ihre Zugangsdaten konnten keinem Benutzer zugeordnet werden"

    Examples:
        | email             | new_email         |
        | test@example.com  | test2@example.com |
        | test2@example.com | test@example.com  |

    @billing
    Scenario Outline: I can change my billing address
        Given I log in successful as "<user>" with email "test@example.com" and password "shopware"
        When  I follow "Rechnungsadresse ändern"
        And   I submit the form "billingForm" on page "Account" with:
            | field         | register[personal] | register[billing] |
            | customer_type | <type>             |                   |
            | salutation    | <salutation>       |                   |
            | company       |                    | <company>         |
            | firstname     | <firstname>        |                   |
            | lastname      | <lastname>         |                   |
            | street        |                    | <street>          |
            | zipcode       |                    | <zipcode>         |
            | city          |                    | <city>            |
            | country       |                    | <country>         |

        Then  I should see "Erfolgreich gespeichert"
        And   the "billing" address should be "<company>, <firstname> <lastname>, <street>, <zipcode> <city>, <country>"

    Examples:
        | user             | type     | salutation | company     | firstname | lastname   | street              | zipcode | city        | country     |
        | Max Mustermann   | private  | ms         |             | Erika     | Musterfrau | Heidestraße 17 c    | 12345   | Köln        | Schweiz     |
        | Erika Musterfrau | business | mr         | shopware AG | Max       | Mustermann | Mustermannstraße 92 | 48624   | Schöppingen | Deutschland |

    @registration
    Scenario: I can create a new account
        Given I am on the homepage
        When  I follow "Mein Konto"
        And   only on "Emotion" template "I press 'Neuer Kunde'"
        And   I submit the form "registrationForm" on page "Account" with:
            | field                | register[personal] | register[billing] | register[shipping] |
            | customer_type        | business           |                   |                    |
            | salutation           | mr                 |                   | ms                 |
            | firstname            | Max                |                   | Erika              |
            | lastname             | Mustermann         |                   | Musterfrau         |
            | email                | a@b.c              |                   |                    |
            | password             | abcdefgh           |                   |                    |
            | passwordConfirmation | ijklmnop           |                   |                    |
            | company              |                    | Muster GmbH       |                    |
            | street               |                    | Musterstr. 55     | Heidestraße        |
            | zipcode              |                    | 55555             | 12345              |
            | city                 |                    | Musterhausen      | Bern               |
            | country              |                    | Deutschland       | Schweiz            |

        Then  I should see "Bitte geben Sie eine gültige eMail-Adresse ein"
        And   I should see "Die Passwörter stimmen nicht überein."
        And   I should see "Bitte füllen Sie alle rot markierten Felder aus"

        When  I submit the form "registrationForm" on page "Account" with:
            | field                | register[personal] |
            | email                | test@example.com   |
            | password             | abc                |
            | passwordConfirmation | abc                |
            | phone                | 05555 / 555555     |

        Then  I should see "Diese eMail-Adresse ist bereits registriert"
        And   I should see "Bitte wählen Sie ein Passwort welches aus mindestens 8 Zeichen besteht."
        But   I should not see "Bitte füllen Sie alle rot markierten Felder aus"

        When  I submit the form "registrationForm" on page "Account" with:
            | field                | register[personal] |
            | email                | test@example.net   |
            | password             | abcdefgh           |
            | passwordConfirmation | abcdefgh           |

        Then  I should see "Willkommen, Max Mustermann"

        When  I follow the link "otherButton" of the element "AccountBilling"
        Then  I should see "Nachdem Sie die erste Bestellung durchgeführt haben, können Sie hier auf vorherige Rechnungsadressen zugreifen."

        When  I follow "Mein Konto"
        And   I follow the link "otherButton" of the element "AccountShipping"
        Then  I should see "Nachdem Sie die erste Bestellung durchgeführt haben, können Sie hier auf vorherige Lieferadressen zugreifen."

        When  I follow "Meine Bestellungen"
        Then  I should see "Sie haben noch keine Bestellung durchgeführt."

        When  I follow "Meine Sofortdownloads"
        Then  I should see "Sie haben noch keine Sofortdownloadartikel gekauft"

    @forgot @login
    Scenario: I can request a new password, if I forgot it
        When  I log in successful as "Max Mustermann" with email "test@example.com" and password "shopware"
        And   I log me out
        And   I follow "Mein Konto"
        And   I follow "Passwort vergessen?"

        Then  I should see "Passwort vergessen? Hier können Sie ein neues Passwort anfordern"
        And   I should see "Wir senden Ihnen ein neues, zufällig generiertes Passwort. Dieses können Sie dann im Kundenbereich ändern."

        When  I fill in "email" with "test@example.info"
        And   I press "Passwort anfordern"
        Then  I should see "Diese E-Mail-Adresse ist uns nicht bekannt"

        When  I fill in "email" with "test@example.com"
        And   I press "Passwort anfordern"
        Then  I should see "Ihr neues Passwort wurde Ihnen zugeschickt"

        When  I follow "Mein Konto"
        And   I log in with email "test@example.com" and password "shopware"
        Then  I should see "Ihre Zugangsdaten konnten keinem Benutzer zugeordnet werden"

        When  I log in with email "test@example.com" and password "shopware"
        Then  I should see "Zu viele fehlgeschlagene Versuche. Ihr Account wurde vorübergehend deaktivert - bitte probieren Sie es in einigen Minuten erneut!"