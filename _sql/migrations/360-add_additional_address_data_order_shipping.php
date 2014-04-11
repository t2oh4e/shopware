<?php
class Migrations_Migration360 Extends Shopware\Components\Migrations\AbstractMigration
{
    public function up()
    {
        $sql = <<<'EOD'
        ALTER TABLE `s_order_shippingaddress` ADD `additional_address_line1` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL ,
        ADD `additional_address_line2` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;
EOD;
        $this->addSql($sql);
    }
}



