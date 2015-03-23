# CF Market Processor



## Tech Used
[Nginx](http://nginx.org/) - HTTP server

[PHP-fpm](http://php-fpm.org) - FastCGI Process Manager for PHP

[Silex](http://silex.sensiolabs.org) - PHP Microframework

[Redis](http://redis.io/) - Key/Value cache and store

[Ratchet](http://socketo.me/) - Websockets for PHP

[libevent](http://libevent.org/) - event notification library

[libevent PHP ext](http://pecl.php.net/package/libevent) - PHP extension

[Supervisord](http://supervisord.org/) - A process control system

[AutobahnJS](http://autobahn.ws/js/) - Open-Source implementation of the Web Application Messaging Protocol (WAMP).

[ZeroMQ](http://zeromq.org/) - Broker-less messaging


## Infrastructure
### Market Processor Droplet 
(1GB, single core)

### Realtime Droplet 
(512MB, single core)

### Redis Droplet 
(512MB, single core)

### Dashboard
(running on same droplet as Market Processor)

## Market Processor Sequence Diagram
![Market Processor Sequence Diagram](http://www.websequencediagrams.com/cgi-bin/cdraw?lz=dGl0bGUgTWFya2V0IFByb2Nlc3NvcgoKVXNlci0-K0VuZHBvaW50OiBQT1NUIHRyYWRlIFxubWVzc2FnZQoAFwgtPisAMwk6IFZhbGlkYXRlZCAAIQcgXG5zZW50IHRvAFgLAGQJLT4rUmVkaXM6IFQAWgVjb25maXJtZWQsIFxuc3RvcmUgaXQKAB4FLT4tAF0LT0sAMRRNZXRyaWNzIFN0b3JlZAAVI1pNUTogU2VuZCBNAIFRBnMgdG8gc29ja2V0ClpNUQBZHC0AghcKQwCBLwgAgW0JSlNPTgCCGAstVXNlcjoAEQUgcmVzcG9uc2UKbm90ZSByaWdodCBvZiAAgQYFWk1RIHNlbmQAgQEGdWJzY3JpYmVyXG4oUmF0Y2hldCkgZm9yIHJlYWwgdGltZSBcbndlYgCBKgYAJQtzCg&s=roundgreen)

## Dashboard Sequence Diagram
![Dashboard Sequence Diagram](http://www.websequencediagrams.com/cgi-bin/cdraw?lz=dGl0bGUgRGFzaGJvYXJkCgpVc2VyLT4rAAkJOiBMb2FkcyBQYWdlCgAfCS0-K0VuZHBvaW50OiBHZXRzIDEwIHJlY2VudCB0cmFkZXMKbm90ZSBsZWZ0IG9mAFcKOiBSABoMIFxuYWRkZWQgdG8gRE9NCgBNCC0tPi0AeAtKU09OIHJlc3BvbnMAeA5SYXRjaGV0OiBXZWJzb2NrZXQgb3BlbmVkLCBcbnN1YnNjcmliZXMgdG8gdG9waWMAgREHcmlnaACBFAUANwlNZXNzYWdlcwCBQQVpdmVkIFxuYnkgWk1RIGFyZSBcbnMAgVcFbyAATAlycwoAdwcAgR8PAG4JZCBtAE4IXG5hcmUgcmV0dXJuZWQAggQZRGF0YSB1cGRhdAB8BWluIHJlYWx0aW0Agm8NVXNlcjoAgw0FAIMYBSBDb21wbGV0ZQ&s=roundgreen)