<?php
use PHPUnit\Framework\TestCase;

class ParseTest extends TestCase
{
    public function testOneTopic()
    {
        $p = new SSPSSP\MQTTTopic\Parser();
        $this->assertTrue($p->match("test", "#"));
        $this->assertTrue($p->match("test", "test"));
        $this->assertTrue($p->match("test", "+"));
        $this->assertTrue($p->match("test", "test/#"));
        $this->assertFalse($p->match("test", "test/+"));
        $this->assertFalse($p->match("test", "test2"));
        $this->assertFalse($p->match("test", "test2/#"));
    }
    public function testTwoTopic()
    {
        $p = new SSPSSP\MQTTTopic\Parser();
        $this->assertTrue($p->match("test/ja", "#"));
        $this->assertFalse($p->match("test/ja", "test"));
        $this->assertFalse($p->match("test/ja", "+"));
        $this->assertTrue($p->match("test/ja", "test/#"));
        $this->assertTrue($p->match("test/ja", "test/+"));
        $this->assertTrue($p->match("test/ja", "test/ja"));
        $this->assertTrue($p->match("test/ja", "test/ja/#"));
        $this->assertTrue($p->match("test/ja", "+/ja"));
        $this->assertTrue($p->match("test/ja", "#/ja"));
    }
    public function testMoreTopic()
    {
        $p = new SSPSSP\MQTTTopic\Parser();
        $this->assertTrue($p->match("test/ja/nein/doch", "#"));
        $this->assertFalse($p->match("test/ja/nein/doch", "test"));
        $this->assertFalse($p->match("test/ja/nein", "+"));
        $this->assertTrue($p->match("test/ja/nein/vielleicht/ja", "test/#"));
        $this->assertFalse($p->match("test/ja/bla", "test/+"));
        $this->assertTrue($p->match("test/ja/bla", "test/+/bla"));
        $this->assertTrue($p->match("test/ja/foor/bar", "test/ja/foor/bar"));
        $this->assertFalse($p->match("test/ja/foo/bar", "+/ja"));
        $this->assertTrue($p->match("test/ja/foo/bar", "#/ja"));
        $this->assertFalse($p->match("test/ja/foo/bar", "test/+"));
        $this->assertFalse($p->match("test/ja/foo/bar", "test/ja/+"));
        $this->assertTrue($p->match("test/ja/foo/bar", "test/+/+/bar"));
    }

    public function testSimpleACL(){
        $p = new SSPSSP\MQTTTopic\Parser();
        $this->assertTrue($p->matchACL("test", "test"));
        $this->assertTrue($p->matchACL("test", "#"));
        $this->assertTrue($p->matchACL("test", "+"));
        $this->assertTrue($p->matchACL("test", "#/bla")); //Invalide
        $this->assertFalse($p->matchACL("test", "+/bla"));
        $this->assertFalse($p->matchACL("test", "someThingElse"));
        $this->assertFalse($p->matchACL("test", "test/someThingElse"));
        $this->assertTrue($p->matchACL("test", "test/#"));
        $this->assertFalse($p->matchACL("test", "test/+"));
        $this->assertFalse($p->matchACL("test", "test/")); //Ja das macht wenig sinn
    }

    public function testMoreACL(){
        $p = new SSPSSP\MQTTTopic\Parser();
        $this->assertTrue($p->matchACL("test/foo", "test/foo"));
        $this->assertTrue($p->matchACL("test/foo", "#"));
        $this->assertFalse($p->matchACL("test/foo", "+"));
        $this->assertTrue($p->matchACL("test/foo", "+/foo"));
        $this->assertTrue($p->matchACL("test/foo", "test/+"));
        $this->assertTrue($p->matchACL("test/foo", "#/bla")); //Invalide
        $this->assertFalse($p->matchACL("test/foo", "+/bla"));
        $this->assertFalse($p->matchACL("test/foo", "someThingElse"));
        $this->assertFalse($p->matchACL("test/foo", "test/someThingElse"));
        $this->assertTrue($p->matchACL("test/foo", "test/#"));
        $this->assertTrue($p->matchACL("test/foo", "test/foo/#"));
        $this->assertFalse($p->matchACL("test/foo", "test/foo/+"));
        $this->assertFalse($p->matchACL("test/foo", "test/foo/")); //Ja das macht wenig sinn
    }
    public function testMoreLevelACL(){
        $p = new SSPSSP\MQTTTopic\Parser();
        $this->assertTrue($p->matchACL("test/foo/bar", "test/foo/bar"));
        $this->assertTrue($p->matchACL("test/foo/bar", "#"));
        $this->assertFalse($p->matchACL("test/foo/bar", "+"));
        $this->assertFalse($p->matchACL("test/foo/bar", "+/+"));
        $this->assertTrue($p->matchACL("test/foo/bar", "+/+/+"));
        $this->assertTrue($p->matchACL("test/foo/bar", "+/foo/bar"));
        $this->assertTrue($p->matchACL("test/foo/bar", "test/+/bar"));
        $this->assertTrue($p->matchACL("test/foo/bar", "test/foo/+"));
        $this->assertTrue($p->matchACL("test/foo/bar", "+/foo/+"));
        $this->assertTrue($p->matchACL("test/foo/bar", "#/bla")); //Invalide
        $this->assertFalse($p->matchACL("test/foo/bar", "+/bla"));
        $this->assertFalse($p->matchACL("test/foo/bar", "+/foo/bla"));
        $this->assertFalse($p->matchACL("test/foo/bar", "someThingElse"));
        $this->assertFalse($p->matchACL("test/foo/bar", "test/someThingElse"));
        $this->assertTrue($p->matchACL("test/foo/bar", "test/#"));
        $this->assertTrue($p->matchACL("test/foo/bar", "test/foo/#"));
        $this->assertTrue($p->matchACL("test/foo/bar", "test/foo/bar/#"));
        $this->assertFalse($p->matchACL("test/foo/bar", "test/foo/bar/+"));
        $this->assertFalse($p->matchACL("test/foo/bar", "test/foo/bar/")); //Ja das macht wenig sinn
    }

    public function testACLWithWildcard() {
        $p = new SSPSSP\MQTTTopic\Parser();
        $this->assertFalse($p->matchACL("#", "test"));
        $this->assertFalse($p->matchACL("+", "test"));
        $this->assertFalse($p->matchACL("#", "+"));
        $this->assertTrue($p->matchACL("+", "#"));
        $this->assertTrue($p->matchACL("test", "#"));
        $this->assertTrue($p->matchACL("test", "+"));
        $this->assertTrue($p->matchACL("test/foo", "#"));
        $this->assertTrue($p->matchACL("test/foo", "test/#"));
        $this->assertTrue($p->matchACL("test/foo", "test/+"));
        $this->assertTrue($p->matchACL("test/foo", "+/foo"));
        $this->assertFalse($p->matchACL("test/foo", "nope/#"));
        $this->assertFalse($p->matchACL("test/foo", "+"));
        $this->assertTrue($p->matchACL("test/foo", "+/+"));
        $this->assertFalse($p->matchACL("test/foo", "+/+/+"));
        $this->assertTrue($p->matchACL("test/foo/bar", "+/+/+"));
        $this->assertTrue($p->matchACL("test/foo/bar", "+/foo/+"));
        $this->assertTrue($p->matchACL("test/foo/bar", "#"));
        $this->assertTrue($p->matchACL("test/foo/bar", "test/#"));
        $this->assertTrue($p->matchACL("test/foo/bar", "test/+/+"));
        $this->assertFalse($p->matchACL("test/foo/bar", "nope/#"));
        $this->assertFalse($p->matchACL("test/foo/bar", "+"));
        $this->assertFalse($p->matchACL("test/foo/bar", "+/+"));
        $this->assertFalse($p->matchACL("test/foo/bar", "test/foo/bar/+"));
        $this->assertFalse($p->matchACL("test/foo/bar", "test/foo/bar/nope"));
    }
}
?>
