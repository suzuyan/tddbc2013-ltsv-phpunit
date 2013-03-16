<?php
class LtsvTest extends \PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->ltsv = new Ltsv();
    }

    function test_設定したKVが取得できる()
    {
        $this->ltsv->set("key", "value");
        $value = $this->ltsv->get("key");
        $this->assertEquals("value", $value);
    }

    function test_設定済みのキーに再設定した場合、元の値が返る()
    {
        $this->assertNull($this->ltsv->set("key", "value1"));
        $this->assertEquals("value1", $this->ltsv->set("key", "value2"));
    }

    function test_設定済みのキーに再設定した場合、末尾に追加される()
    {
        $this->ltsv->set("key1", "value1");
        $this->ltsv->set("key2", "value2");
        $this->ltsv->set("key1", "new_value1");
        $this->assertEquals("key2:value2\tkey1:new_value1\n", $this->ltsv->dump());
    }

    function test_dumpするとLTSV形式で返る()
    {
        $this->ltsv->set("key1", "value1");
        $this->ltsv->set("key2", "value2");
        $this->assertEquals("key1:value1\tkey2:value2\n", $this->ltsv->dump());

        $this->ltsv->set("key3", "value3");
        $this->assertEquals("key1:value1\tkey2:value2\tkey3:value3\n", $this->ltsv->dump());
    }

    function test_設定されていないキーを取得した場合、NULLが返る()
    {
        $value = $this->ltsv->get("key1");
        $this->assertNull($value);
    }

    function test_キーがNULLの場合、例外を投げる()
    {
        $this->setExpectedException("InvalidArgumentException", "キーにNULLが設定されています");
        $this->ltsv->set(null, "value");
    }

    function test_キーが空文字列の場合、例外を投げる()
    {
        $this->setExpectedException("InvalidArgumentException", "キーに空文字列が設定されています");
        $this->ltsv->set("", "value");
    }

    function test_値がNULLの場合、例外を投げる()
    {
        $this->setExpectedException("InvalidArgumentException", "値にNULLが設定されています");
        $this->ltsv->set("key", null);
    }

    /**
     * @dataProvider provideEscapePatterns
     */
    function test_KVに特殊文字が入っていた場合、エスケープする($key, $value, $ltsv)
    {
        $this->ltsv->set($key, $value);
        $this->assertEquals($ltsv, $this->ltsv->dump());
    }

    function provideEscapePatterns()
    {
        return [ 
            [":key",  ":value",  "\:key:\:value\n"],
            ["key\t", "value\t", "key\\t:value\\t\n"],
            ["key\n", "value\n", "key\\n:value\\n\n"],
        ];
    }

    function test_パースしたらLTSVのインスタンスが返る()
    {
        $ltsv = Ltsv::parse("key:value\tkey2:value2\n");
        $this->assertInstanceOf("Ltsv", $ltsv);
    }

    function test_パースしダンプしたら元の文字列と一致する()
    {
        $dump_str = "key:value\tkey2:value2\n";
        $ltsv = Ltsv::parse($dump_str);
        $this->assertEquals($dump_str, $ltsv->dump());
    }
}

