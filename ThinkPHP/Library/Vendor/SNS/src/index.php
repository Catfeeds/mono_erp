<?php
/**
 * 功能：通过email查询用户SNS信息
 * 接口:pipl https://www.pipl.com/
 * 使用方法： 1.引入该文件; 2.调用getSns($email,$first,$last)获得结果;
 * 参数： 1.$email 用户邮箱; 2.$first firstname; 3.$laat lastname;
 * 返回值：  Array
 * (
 *     [match] => 1     //1:确定信息 0：可能信息
 *     [sns_id] => Array        //社交网络账号
 *         (
 *             [0] => #40144628@linkedin
 *             [1] => 1555081628@facebook
 *             [2] => 96640705@linkedin
 *             [3] => 28/446/401@linkedin
 *         )
 * 
 *     [url] => Array       //社交网络个人页面地址
 *         (
 *             [0] => http://facebook.com/people/_/1555081628
 *             [1] => http://www.linkedin.com/pub/mary-ling/28/446/401
 *             [2] => http://www.facebook.com/mary.ling.737
 *         )
 * )
 * 接口配置：search.php/class PiplApi_SearchRequestConfiguration/function __construct
 *      1.可以在此更改$api_key
 *      2.参考：https://pipl.com/dev/
 * 注意：批量执行时，循环一次要sleep(1),原因是pipl设置了 每秒请求次数 限制
 * 测试：  1.  api_key:lv1  country:US  total:100  return:52
 *      
 */
require_once VENDOR_PATH.'/SNS/src/piplapis/search.php';//根据实际情况修改路径/test
error_reporting(0);
set_time_limit(0);

function getSns($email)//,$first,$last
{
    $fields = array(
        new PiplApi_Email(array("address"=>$email)),
    );
    $one_person = new PiplApi_Person($fields);
    $request = new PiplApi_SearchAPIRequest(array('person' => $one_person));
    
    try {
        $response = $request->send(false);
    } catch (PiplApi_SearchAPIError $e) {
        print $e->getMessage();
    } catch (InvalidArgumentException $e) {
        print $e->getMessage();
    }
    $response = json_decode($response);
    
    $return = array();
    if($response->person)
    {
        $person = $response->person;
        $return['match'] = true;
    }
    else if($response->possible_persons)
    {
        $person = $response->possible_persons[0];
        $return['match'] = false;
    }

    if($person)
    {
        $user_ids = $person->user_ids;
        if($user_ids)
        {
            for ($i=0;$i<sizeof($user_ids);$i++)
            {
                $return['sns_id'][] = $user_ids[$i]->content;
            }
        }
    
        $urls = $person->urls;
        if($urls)
        {
            for ($i=0;$i<sizeof($urls);$i++)
            {
                $return['url'][] = $urls[$i]->url;
            }
        }
    }
    return $return;
}

