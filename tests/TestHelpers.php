<?php
/**
 * Created by PhpStorm.
 * User: fvasquez
 * Date: 1/10/18
 * Time: 01:40 PM
 */

namespace Tests;


trait TestHelpers
{
    protected function assertDatabaseEmpty($table,$connection=null)
    {
        $total = $this->getConnection($connection)->table($table)->count();
        $this->assertSame(0,$total,sprintf(
            "Failed asserting the table [%s] is empty. %s %s found.", $table,$total,str_plural('row',$total)
        ));
    }


    /**
     * @param array $custom
     * @return array
     */
    protected function withData(array $custom=[])
    {
        return array_merge($this->defaultData(), $custom);
    }

    protected function defaultData()
    {
        return $this->defaultData;
    }
}