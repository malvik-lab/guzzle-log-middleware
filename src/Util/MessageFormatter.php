<?php

namespace GuzzleLogMiddleware\Util;

class MessageFormatter {
    const FORMAT_01 = '{hostname} {req_header_User-Agent} - "{method} {host} {target} HTTP/{version}" {code} {res_header_Content-Length}';
}