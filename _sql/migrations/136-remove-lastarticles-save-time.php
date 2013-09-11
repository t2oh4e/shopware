<?php
class Migrations_Migration136 Extends Shopware\Components\Migrations\AbstractMigration
{
    public function up()
    {
        $sql = <<<'EOD'
UPDATE `s_core_snippets` SET `value` = 'Zuletzt angesehen' WHERE `name` = 'WidgetsRecentlyViewedHeadline' AND `value` = 'Zuletzt angeschaute Artikel';
EOD;
        $this->addSql($sql);
    }
}
