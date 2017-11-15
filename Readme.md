# Doc
## Parse topic
Check if a Message-Topic matches to a subscription rule.

First Parameter is the Topic where the Message is published to (no wildcard).
The second the Subscription Rule (with wildcard).

```
$p = new SSPSSP\MQTTTopic\Parser();
$p->match("test", "#"); //Returns true
$p->match("test", "test"); //Returns true
$p->match("test", "+"); //Returns true
$p->match("test", "some/other/subscription/rule"); //Returns false
```

## Check ACL
To check if it is possible to subscribe a topic with a given ACL-Rule.

The first Parameter is the topic the client want to subscribe (with wildcard)
The second Parameter is the ACL-Rule to check if the subscription is possible (with wildcard)

```
$p = new SSPSSP\MQTTTopic\Parser();
$p->matchACL("foo", "#"); //Returns true
$p->matchACL("foo/#", "#"); //Returns true
$p->matchACL("foo/bar", "+"); //Returns false
$p->matchACL("test", "some/other/subscription/rule"); //Returns false
```
