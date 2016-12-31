<?php

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

class MysqlToMysqliFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideExamples()
    {
        return array(
            array('<?php $x = 1;'),
            array('<?php $x = "mysql";'),
            // mysql_connect
            array('<?php $x = mysqli_connect();', '<?php $x = mysql_connect();'),
            array('<?php $x = mysqli_connect(\'127.0.0.1\');', '<?php $x = mysql_connect(\'127.0.0.1\');'),
            array('<?php $x = mysqli_connect(\'127.0.0.1\', \'myUser\');', '<?php $x = mysql_connect(\'127.0.0.1\', \'myUser\');'),
            array('<?php $x = mysqli_connect(\'127.0.0.1\', \'myUser\', \'myP4ssw0rd\');', '<?php $x = mysql_connect(\'127.0.0.1\', \'myUser\', \'myP4ssw0rd\');'),
            array('<?php $x = mysql_connect(\'127.0.0.1\', \'myUser\', \'myP4ssw0rd\', true);'),
            array('<?php $x = mysql_connect(\'127.0.0.1\', \'myUser\', \'myP4ssw0rd\', true, MYSQL_CLIENT_SSL);'),
            // mysql_affected_rows
            array('<?php mysql_affected_rows();'),
            array('<?php mysqli_affected_rows($link);', '<?php mysql_affected_rows($link);'),
            // mysql_client_encoding
            array('<?php mysql_client_encoding();'),
            array('<?php mysqli_character_set_name($link);', '<?php mysql_client_encoding($link);'),
            // mysql_close
            array('<?php mysql_close();'),
            array('<?php mysqli_close($link);', '<?php mysql_close($link);'),
            // mysql_data_seek
            array('<?php mysqli_data_seek($result, $offset);', '<?php mysql_data_seek($result, $offset);'),
            // mysql_errno
            array('<?php mysql_errno();'),
            array('<?php mysqli_errno($link);', '<?php mysql_errno($link);'),
            // mysql_errno
            array('<?php mysql_error();'),
            array('<?php mysqli_error($link);', '<?php mysql_error($link);'),
            // mysql_fetch_array
            array('<?php mysql_fetch_array($result, MYSQL_BOTH);'),
            array('<?php mysqli_fetch_array($result);', '<?php mysql_fetch_array($result);'),
            // mysql_fetch_assoc
            array('<?php mysqli_fetch_assoc($result);', '<?php mysql_fetch_assoc($result);'),
            // mysql_fetch_field
            array('<?php mysql_fetch_field($result, 0);'),
            array('<?php mysqli_fetch_field($result);', '<?php mysql_fetch_field($result);'),
            // mysql_fetch_lengths
            array('<?php mysqli_fetch_lengths($result);', '<?php mysql_fetch_lengths($result);'),
            // mysql_fetch_object
            array('<?php mysqli_fetch_object($result);', '<?php mysql_fetch_object($result);'),
            array('<?php mysqli_fetch_object($result, \'AClassName\');', '<?php mysql_fetch_object($result, \'AClassName\');'),
            array('<?php mysqli_fetch_object($result, \'AClassName\', array(1, "test"));', '<?php mysql_fetch_object($result, \'AClassName\', array(1, "test"));'),
            // mysql_fetch_row
            array('<?php mysqli_fetch_row($result);', '<?php mysql_fetch_row($result);'),
            // mysql_field_seek
            array('<?php mysqli_field_seek($result, 0);', '<?php mysql_field_seek($result, 0);'),
            // mysql_free_result
            array('<?php mysqli_free_result($result);', '<?php mysql_free_result($result);'),
            // mysql_get_host_info
            array('<?php mysql_get_host_info();'),
            array('<?php mysqli_get_host_info($link);', '<?php mysql_get_host_info($link);'),
            // mysql_get_proto_info
            array('<?php mysql_get_proto_info();'),
            array('<?php mysqli_get_proto_info($link);', '<?php mysql_get_proto_info($link);'),
            // mysql_get_server_info
            array('<?php mysql_get_server_info();'),
            array('<?php mysqli_get_server_info($link);', '<?php mysql_get_server_info($link);'),
            // mysql_info
            array('<?php mysql_info();'),
            array('<?php mysqli_info($link);', '<?php mysql_info($link);'),
            // mysql_insert_id
            array('<?php mysql_insert_id();'),
            array('<?php mysqli_insert_id($link);', '<?php mysql_insert_id($link);'),
            // mysql_num_fields
            array('<?php mysqli_num_fields($result);', '<?php mysql_num_fields($result);'),
            // mysql_num_rows
            array('<?php mysqli_num_rows($result);', '<?php mysql_num_rows($result);'),
            // mysql_ping
            array('<?php mysql_ping();'),
            array('<?php mysqli_ping($link);', '<?php mysql_ping($link);'),
            // mysql_ping
            array('<?php mysql_stat();'),
            array('<?php mysqli_stat($link);', '<?php mysql_stat($link);'),
            // mysql_thread_id
            array('<?php mysql_thread_id();'),
            array('<?php mysqli_thread_id($link);', '<?php mysql_thread_id($link);'),
        );
    }

    public function testMultipleOcurrences()
    {
        $this->makeTest(
            '<?php $x = mysqli_connect(); $y = mysqli_connect(); $z = mysqli_connect();',
            '<?php $x = mysql_connect(); $y = mysqli_connect(); $z = mysql_connect();'
        );
    }
}
