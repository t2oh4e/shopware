<?php
class Migrations_Migration366 Extends Shopware\Components\Migrations\AbstractMigration
{
    public function up()
    {
        $sql = <<<'EOD'
            SET @formId = (SELECT id FROM s_core_config_forms WHERE name = 'TrustedShop');
            UPDATE `s_core_config_form_translations`
            SET `description` = '\r\n<style type="text/css">\r\n    body { padding: 10px; }\r\n    h2 { margin: 0 0 .5em; }\r\n    p { margin: 0 0 1.5em }\r\n    .logo { margin: 1em; }\r\n    .logo img { display: block; width: 300px; height: 140px; margin: 0 auto }\r\n</style>\r\n\r\n<div class="logo">\r\n	<img src=''data:image/gif;base64,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\r\nOw=='' alt="Trusted Shops Logo">\r\n</div>\r\n\r\n<div class="info">\r\n	<h2>Seal of approval and buyer protection</h2>\r\n	<p>\r\n		Trusted Shops certifies online shops by carefully checking a SET of quality criteria before awarding the European trust mark. With the combination of Trusted Shop\'s money-back guarantee and the seller rating system you are able to order online with confidence.\r\n	</p>\r\n\r\n\r\n	<h2>More trust means more turnover!</h2>\r\n	<p>\r\n		The Trusted Shops Seal of Approval is optimal for building the trust of your online customers. Confidence increases the readiness of your customers to buy from you.\r\n	</p>\r\n\r\n	<h2>Fewer abandoned shopping carts</h2>\r\n	<p>\r\n		You offer your online customers a strong argument: the Trusted Shops Buyer Protection. With this additional security, fewer purchases will be cancelled.\r\n	</p>\r\n\r\n	<h2>Profitable and sustainable customer relationships</h2>\r\n	<p>\r\n		The Trusted Shops Seal of Approval with Buyer Protection is a sustainable quality feature for safe shopping on the Web for many online shoppers. First-time buyers become regular customers.\r\n	</p>\r\n	<div class="register">\r\n		<a class="button" href="https://www.trustedshops.co.uk/merchants/" target="_blank">\r\n			Get informed and sign up!\r\n		</a>\r\n	</div>\r\n</div>'
            WHERE form_id = @formId
            AND `description` = '\r\n<style type="text/css">\r\n    body { padding: 10px; }\r\n    h2 { margin: 0 0 .5em; }\r\n    p { margin: 0 0 1.5em }\r\n    .logo { margin: 1em; }\r\n    .logo img { display: block; width: 300px; height: 140px; margin: 0 auto }\r\n</style>\r\n\r\n<div class="logo">\r\n	<img src=''data:image/gif;base64,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\r\nOw=='' alt="Trusted Shops Logo">\r\n</div>\r\n\r\n<div class="info">\r\n	<h2>Seal of approval and buyer protection</h2>\r\n	<p>\r\n		Trusted Shops certifies online shops by carefully checking a SET of quality criteria before awarding the European trust mark. With the combination of Trusted Shop\'s money-back guarantee and the seller rating system you are able to order online with confidence.\r\n	</p>\r\n\r\n\r\n	<h2>More trust means more turnover!</h2>\r\n	<p>\r\n		The Trusted Shops Seal of Approval is optimal for building the trust of your online customers. Confidence increases the readiness of your customers to buy from you.\r\n	</p>\r\n\r\n	<h2>Fewer abandoned shopping carts</h2>\r\n	<p>\r\n		You offer your online customers a strong argument: the Trusted Shops Buyer Protection. With this additional security, fewer purchases will be cancelled.\r\n	</p>\r\n\r\n	<h2>Profitable and sustainable customer relationships</h2>\r\n	<p>\r\n		The Trusted Shops Seal of Approval with Buyer Protection is a sustainable quality feature for safe shopping on the Web for many online shoppers. First-time buyers become regular customers.\r\n	</p>\r\n	<div class="register">\r\n		<a class="button" href="http://www.trustedshops.de/shopbetreiber/index.html?et_cid=14&et_lid=29818" target="_blank">\r\n			Get informed and sign up!\r\n		</a>\r\n	</div>\r\n</div>';

            SET @elementId = (SELECT id FROM s_core_config_elements WHERE name = 'logMail');
            INSERT IGNORE INTO `s_core_config_element_translations` (`element_id`, `locale_id`, `label`, `description`)
            VALUES (@elementId, '2', 'Report errors to shop owner', NULL);

            SET @elementId = (SELECT id FROM s_core_config_elements WHERE name = 'preferBasePath');
            INSERT IGNORE INTO `s_core_config_element_translations` (`element_id`, `locale_id`, `label`, `description`)
            VALUES (@elementId, '2', 'Remove "Shopware" from URLs', 'Remove "shopware.php" from URLs. Prevents search engines from incorrectly identifying duplicate content in the shop. If mod_rewrite is not available in Apache, this option must be disabled.');

            SET @elementId = (SELECT id FROM s_core_config_elements WHERE name = 'moveBatchModeEnabled');
            INSERT IGNORE INTO `s_core_config_element_translations` (`element_id`, `locale_id`, `label`, `description`)
            VALUES (@elementId, '2', 'Move categories in batch mode', NULL);

            UPDATE s_core_config_element_translations
            SET label='Automatically remind customer to submit reviews', description='Remind the customer via email of pending article reviews'
            WHERE label='Automatically reminder customer to submit reviews' AND description='';

            UPDATE s_core_config_element_translations
            SET description='Days until the customer is reminded via Email of a pending article review'
            WHERE label='Days to wait before sending reminder' AND description='';
EOD;

        $this->addSql($sql);
    }
}
