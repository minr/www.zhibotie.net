<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include_once(S_ROOT."./apps/oauth/inc/OAuthBase.php");
include_once(S_ROOT."./apps/oauth/inc/HttpUtil.php");
class OauthCommon extends OauthBase{
    private $appkey;
    private $appkeysercert;
    private $request_token_uri;
    private $authorize_uri;
    private $access_token_uri;
    
    public function __construct($appkey,$appkeysercert,$request_token_uri,$authorize_uri,$access_token_uri){
        $this->appkey = $appkey;
        $this->appkeysercert = $appkeysercert;
        $this->request_token_uri = $request_token_uri;
        $this->authorize_uri = $authorize_uri;
        $this->access_token_uri = $access_token_uri;
    }
    
    public function  GetAppKey()
    {
        return $this->appkey;
    }
    
    public function GetAppKeySercert()
    {
        return $this->appkeysercert;
    }
    
    public function GetRequestTokenUri()
    {
        return $this->request_token_uri;
    }
    
    public function GetAuthorizeUri(){
        return $this->authorize_uri;
    }
    
    public function GetAccessTokenUri()
    {
        return $this->access_token_uri;
    }
    
    public function RequestToken($callback_uri,$para)
    {
        $uri = $this->GetOauthUrl($this->GetRequestTokenUri(), "Get",$this->GetAppKey(),  $this->GetAppKeySercert(),"","","",$callback_uri,$para);
        $http = new Http();
        if(is_array($uri)){
            $url = $uri[0];
            if($uri[1] !=""){
                $url.="?".$uri[1];
                return $http->fetch_page($url,false,"Get");
            }
        }else{
            return "";
        }
    }
    
    public function AuthorizationURL($oauth_token) 
    {
         $getUserAuthorizationURL =$this->GetAuthorizeUri(). "?oauth_token=" .$oauth_token;
         return $getUserAuthorizationURL;
    }
    
    public function GetAccessToken($verifier,$oauth_token,$oauth_token_sercet,$parameters = array())
    {
        $uri = $this->GetOauthUrl($this->GetAccessTokenUri(), "Get",$this->GetAppKey() , $this->GetAppKeySercert(), $oauth_token,$oauth_token_sercet,$verifier, "", $parameters);
        $http = new Http();
        if(is_array($uri)){
            $url = $uri[0];
            if($uri[1] !=""){
                $url.="?".$uri[1];
                return $http->fetch_page($url,false,"Get");
            }
        }else{
            return "";
        }
    }
    
    public function SignRequest($uri, $HttpMode, $postData, $access_token, $access_token_sercert){
        $postUri = $this->GetOauthUrl($uri, $HttpMode, $this->GetAppKey() ,$this->GetAppKeySercert(),  $access_token, $access_token_sercert,"" , "", $postData);
        $http = new Http();
        if(is_array($postUri)){
            if(strtoupper($HttpMode) == "GET"){
                $url = $postUri[0]."?".$postUri[1];
                return $http->fetch_page($url,false,$HttpMode);
            }  
            else if(strtoupper($HttpMode) == "POST") {
                $url = $postUri[0];
                return $http->fetch_page($url,$postUri[1],$HttpMode);
            }  else {
                return "";
            }           
        }else{
            return "";
        }
    }
    
    public function SignXMLRequest($uri, $HttpMode, $postData, $access_token, $access_token_sercert){
        $para = array();
        $postUri = $this->GetOauthUrl($uri, $HttpMode, $this->GetAppKey() ,$this->GetAppKeySercert(),  $access_token, $access_token_sercert,"" , "",  $para);
        $http = new Http();
        if(is_array($postUri)){
            $para = $http->GetQueryParameters($postUri[1]);
            $header = $this->getAuthorizationHeader($para, "");
            if(strtoupper($HttpMode) == "POST") {
                $url = $postUri[0];
                $header = array('Content-Type: application/atom+xml',$header);
                return $http->fetch_header_page($url, $header, $postData);
            }  else {
                return "";
            }
        }  else {
            return "";
        }
      
    }
    
    public function getAuthorizationHeader($para,$realm){
        $header = "Authorization: OAuth realm=\"" .$realm. "\"";
        foreach ($para as $key=>$value){
             if(strpos($key,"oauth_") >- 1){
                  $header .= ",".$key."=".$value;
             } 
        }
        return $header;
    }
    
    public function Sign($para,$session_sercert=false){
        if(is_array($para)){
            uksort($para, 'strcmp');
            $sbList="";
            foreach ($para as $k=>$v){
                $sbList.=$k."=".$v;
            }
            if($session_sercert){
                $sbList.=$session_sercert;
            }else{
                $sbList.=$this->appkeysercert;
            }
            return md5($sbList);
        }else{
            return "";
        }
    }
    
    public function GetAuthorizationCode($callback_uri,$scope){
        $para=array("client_id"=>  $this->appkey,"response_type"=>"code","redirect_uri"=>$callback_uri);
        if($scope !=""){
          $para["scope"] = $scope;
        }
        $AuthorizationUri = $this->request_token_uri."?".$this->NormalizeRequestParameters($para);
        return $AuthorizationUri;
    }
    
    public function Get2AccessToken($AuthorizationCode,$callback_uri){
        $para = array("grant_type"=>"authorization_code");
        $para["code"] = $AuthorizationCode;
        $para["client_id"] = $this->appkey;
        $para["client_secret"] = $this->appkeysercert;
        $para["redirect_uri"] = $callback_uri;
        $AccessUrl = $this->authorize_uri."?".$this->NormalizeRequestParameters($para);
        $http = new Http();
        return $http->fetch_page($AccessUrl,"","POST");
    }
    
    public function GetSessionKey($access_token){
         $para = array("oauth_token"=>$access_token);
         $SessionUrl = $this->access_token_uri."?".$this->NormalizeRequestParameters($para);
         $http = new Http();
         return $http->fetch_page($SessionUrl,"","POST");
    }
    
    public function CallRequest($uri, $paras, $session_key,$format,$server,$session_sercert){
        $strSign="";
        $http=new Http();
        if(is_array($paras)){
            $paras["session_key"] = $session_key;
            $paras["format"] = $format!=""?$format:"json";
            if(strtoupper($server) == "RENREN"){
                $paras["api_key"] =$this->appkey;
                $paras["call_id"] = floor(microtime()*1000);
                $paras["v"] = "1.0";
                $strSign = $this->Sign($paras,false); 
                $paras["sig"] =$strSign;
                $call_uri = $uri."?".$this->NormalizeRequestParameters($paras);
                return $http->fetch_page($call_uri, $paras, "post");
            }else if(strtoupper($server) == "BAIDU"){
                $paras["timestamp"] = date("Y-m-d h:i:s");
                $strSign = $this->Sign($paras, $session_sercert);
                $paras["sign"] = $strSign;
                $call_uri = $uri."?".$this->NormalizeRequestParameters($paras);
                return $http->fetch_page($call_uri, $paras, "post");
            }else{
                return $strSign;
            }
        }else{
            return $strSign;
        }
    }
}
?>
