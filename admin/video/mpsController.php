<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/aliyun-php-sdk/aliyun-php-sdk-core/Config.php';
use Mts\Request\V20140618 as Mts;
$access_key_id = 'LTAIIwQ9XuZ4abJq';
$access_key_secret = 'u9RUQTZWkXZg603NUgQTDBt4yuLG8s';
$mps_region_id = 'cn-hangzhou';
$pipeline_id = '46a2d183205d4e1b9553bc5b6e560c74';
$template_id = 'S00000001-100000';
$oss_location = 'oss-cn-hangzhou';
$oss_bucket = 'test-bucket-hz';
$oss_input_object = 'input.mp4';
$oss_output_object = 'output.mp4';
# 创建DefaultAcsClient实例并初始化
$clientProfile = DefaultProfile::getProfile(
    $mps_region_id,                   # 您的 Region ID
    $access_key_id,                   # 您的 AccessKey ID
    $access_key_secret                # 您的 AccessKey Secret
);
$client = new DefaultAcsClient($clientProfile);
# 创建API请求并设置参数
$request = new Mts\SubmitJobsRequest();
$request->setAcceptFormat('JSON');
# Input
$input = array('Location' => $oss_location,
    'Bucket' => $oss_bucket,
    'Object' => urlencode($oss_input_object));
$request->setInput(json_encode($input));
# Output
$output = array('OutputObject' => urlencode($oss_output_object));
# Ouput->Container
$output['Container'] = array('Format' => 'mp4');
# Ouput->Video
$output['Video'] = array('Codec' =>'H.264',
    'Bitrate' => 1500,
    'Width' => 1280,
    'Fps' => 25);
# Ouput->TemplateId
$output['TemplateId'] = $template_id;
$outputs = array($output);
$request->setOutputs(json_encode($outputs));
$request->setOutputBucket($oss_bucket);
$request->setOutputLocation($oss_location);
# PipelineId
$request->setPipelineId($pipeline_id);
# 发起请求并处理返回
try {
    $response = $client->getAcsResponse($request);
    print 'RequestId is:' . $response->{'RequestId'} . "\n";;
    if ($response->{'JobResultList'}->{'JobResult'}[0]->{'Success'}) {
        print 'JobId is:' .
            $response->{'JobResultList'}->{'JobResult'}[0]->{'Job'}->{'JobId'} . "\n";
    } else {
        print 'SubmitJobs Failed code:' .
            $response->{'JobResultList'}->{'JobResult'}[0]->{'Code'} .
            ' message:' .
            $response->{'JobResultList'}->{'JobResult'}[0]->{'Message'} . "\n";
    }
} catch(ServerException $e) {
    print 'Error: ' . $e->getErrorCode() . ' Message: ' . $e->getMessage() . "\n";
} catch(ClientException $e) {
    print 'Error: ' . $e->getErrorCode() . ' Message: ' . $e->getMessage() . "\n";
}