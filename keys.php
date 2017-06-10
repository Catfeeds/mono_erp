<?php
/*  ?2013 eBay Inc., All Rights Reserved */ 
/* Licensed under CDDL 1.0 -  http://opensource.org/licenses/cddl1.php */
    //show all errors
    error_reporting(E_ALL);

    // these keys can be obtained by registering at http://developer.ebay.com
    
    $production         = true;   // toggle to true if going against production
    $compatabilityLevel = 717;    // eBay API version
    
    if ($production) {
        $devID = '10a1ffff-8d7a-43b1-9285-eacdfd6baafc';   // these prod keys are different from sandbox keys
        $appID = 'panwanlo-lilyebay-PRD-32f839365-e6f57fe6';
        $certID = 'PRD-2f839365d86e-b953-4908-8c17-a33f';
        //set the Server to use (Sandbox or Production)
        $serverUrl = 'https://api.ebay.com/ws/api.dll';      // server URL different for prod and sandbox
        //the token representing the eBay user to assign the call with
        $userToken = 'AgAAAA**AQAAAA**aAAAAA**0jE1Vw**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AFk4KjDJCLpwSdj6x9nY+seQ**lEgDAA**AAMAAA**qMckJrQNywdrybqXH8o7kePghkCL13lyXcYvO7vZEgqOmks19BxoZ4J2YCXmP9uaA4uZozU6ZWRU0dyBtuKI9a1n0IYdG0ZvArSqF6X1eQixcggiiCn8rL181RTSAdwOxdzOM4bd5I/SqyR0Kw2bTCRcmHA0mTjHg+3GsC4b7/e5P4r6in0R+xbXyzDWFdDrWG7w9tpIiZeA5tuGOXVxyeV5PLoFsMgTq2Im8itpWDTYkPhhvtgzlWE8JKXOOqNOsNpSMsg7LMZ5NvhrTl22xheeIWa0HhXWMy6ZRmueKKTdXLCgLRJ0VIUVszVL80zjU+s2soq+5FZ1zspX6wmfdeF/Jq6Me80jQqc40JLJfGgJK9o8Fytjy9gNuUL+495xc1jze5avgPLN95jNhp9h1DN7tVauGjlLQTUdymKXVVKyoD0Ef88JhjzB2/6+TxdOPAHwVWgqOvl4uvOEel9vcdwZ0fRB9ZOnc6CDNmxiXPeaSSL6/B2vY8S/ip0An7ghfsXMW365AiPHyqxk2g7NLhJHMTBwAvxqsIlhXQK5vVfkYPc5lK5p8S3veLczSlTw8ulI4sxrfcHESCpqFgKHd0YekdS6MKA0FJSCL65G06tnwTu7zuhbqJwoHiTW1IlqSXbGk+DfZpHXXNT+/BjSNDaGHXNHOmGv8fx5G/f2ZoZSkpxYLE2HYzcuPT4ey60UOAZ4yvI5fq2jy1YysQfJTwxiKIPiBNTN1J48aEvoc/WDmXvKNaMf8a0lnf0Tzavu';          
    } else {  
        // sandbox (test) environment
        $devID = 'panwanlo-lilyebay-SBX-d90ed7b6b-69ba41d9';   // these SB keys are different from prod keys
        $appID = '10a1ffff-8d7a-43b1-9285-eacdfd6baafc';
        $certID = 'SBX-90ed7b6b0b17-5da6-45c8-a1bb-f687';
        //set the Server to use (Sandbox or Production)
        $serverUrl = 'https://api.sandbox.ebay.com/ws/api.dll';
        // the token representing the eBay user to assign the call with
        // this token is a long string - don't insert new lines - different from prod token
        $userToken = 'AgAAAA**AQAAAA**aAAAAA**jy01Vw**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFk4GgDJCBowydj6x9nY+seQ**F88DAA**AAMAAA**SKzx1ESMur+XOehqqtcTEZnO6l/xD0rD348wBSVPu/zlET2Swv03t9UOmVScZJC1MHx+mp4xu83e7Y6Iq2zvgCc5YAn1RMAJLvc9k6fFWm0R9I9mxcW1cxlfnnyEoXvNXGgTb7Fbn1JNOnlYQsD0LmeS9oxZE6cK6vvv2uR6WjAG8qohXl87JxE2xKqVP6EYQK39PPTXorEN/zCtV/Jd1xcpMTF+BQKnFRHsQM+elcYjqRnJJbI4j8omJFRyYCzRsem++a61BOTRITsmubhLpg2vyttPFDUsI3SH5toGyQLJOeFRaWaXdexwnbbIQoud0skjHcrXL+MI+ODL+q49hF77PKJMO8cyAOb70S1d6I48xll60tGqO0gOhlleSsJgERAtA87jQ1O/CZgCSPWSsYoaDzqllwO3ayc52BqXCNRyXOSu1a6V3YTHMSEkgWoVjau6kuQkGGolyQF1rM4rZc41FlGmMSJH6w6QYpH/O5NegkJfEhc1SLbKcTbvl1PHrx3LcF1nCbS4yylujP60yyu4AoNF7+QbeonoaBBnUHivmyUUuUmyzz/2R/MSe9B4NK97tItHOtlM97osp6p2lhDa94xUHGQzms4D4e5i8dzi/7aGWMFyRv3G5D1QrxvrrzmjjpQA/9EpIFYBDA+tUxxWQ1BaGN5/pIK+vBNgQPuQF/lY70/pG3CIh3Yl5r2nTOwX0n36lJup1Xg+Nil40vKhS2RfZB+95yhgcd41qvl9IbjgmjoNCFpXcbOuExUg';          
    }
    
    
?>