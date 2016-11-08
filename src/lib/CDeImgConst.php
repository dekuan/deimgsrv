<?php 
namespace dekuan\deimgsrv\lib;

class CDeImgConst
{

	const URL_IMG_CENTER				= 'http://img.dekuan.org';		//	图片服务器请求url

	const CONST_SRV_URI_TOKEN 			= '/gettoken';					//	img服务器获得token uri
	const CONST_SRV_URI_IMG_URL_TO_OSS 	= '/upimgbyurl';				//	img服务器,通过给定url上传图片到oss uri
	const CONST_SRV_URI_IMG_URL		 	= '/showimg';				//	img服务器,通过给定imgName,302到阿里oss地址

	const CONST_ACCESS_KEY_ID 			= 'AccessKeyId';            	//	accessKey对应key值
	const CONST_ACCESS_KEY_SECRET 		= 'AccessKeySecret';      		//	accessKeySecret对应key值
	const CONST_TOKEN_EXPIRATION 		= 'Expiration';            		//	token有效期对应key值
	const CONST_TOKEN 					= 'SecurityToken';            	//	token对应key值

	const CONST_EXPIRATION_MIN_TIME 	= 100;							//	token剩余最小有效时间

	const CONST_FILE_UP_NAME 			= 'dkfile';						//	上传文件,input name
	const CONST_FILE_UP_TMP_DIR 		= 'pictures/';					//	临时文件目录地址
	const CONST_IMG_BUCKET 				= 'deimage';					//	image bucket名字

	const CONST_DEFAULT_IF_USE_INNER 	= self::CONST_USE_INNER;		//	是否使用内网;
	const CONST_NOT_USE_INNER 			= 0;							//	使用外网标识;与deoss包中const值保持一致,请勿修改
	CONST CONST_USE_INNER 				= 1;							//	使用内网标识;与deoss包中const值保持一致,请勿修改
	CONST CONST_REQUEST_TIMEOUT_DEFAULT	= 5;							//	请求统一默认超时时间
}
