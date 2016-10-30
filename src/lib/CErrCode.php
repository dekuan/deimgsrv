<?php
namespace dekuan\deimgsrv;

use dekuan\vdata\CConst;

class CErrCode extends CConst{
	const ERR_UNKNOWN 	= self::ERROR_UNKNOWN;
	const ERR_SUCC		= self::ERROR_SUCCESS ;

	const ERR_BASE		= -200000;

	const ERR_TOKEN_REQUEST_URL				= self::ERR_BASE - 1;				//	请求token,url参数错误
	const ERR_TOKEN_REQUEST_URI				= self::ERR_BASE - 2;				//	请求token,uri参数错误
	const ERR_TOKEN_REQUEST_TIMEOUT			= self::ERR_BASE - 3;				//	请求token,超时时间参数错误
	const ERR_TOKEN_REQUEST_APP_ID			= self::ERR_BASE - 3;				//	请求token,appID参数错误
	const ERR_TOKEN_REQUEST_NETWORK			= self::ERR_BASE - 4;				//	请求token,请求网络错误
	const ERR_TOKEN_REQUEST_RESPONSE		= self::ERR_BASE - 5;				//	请求token,响应参数错误
	const ERR_TOKEN_REQUEST					= self::ERR_BASE - 6;				//	请求token,请求接口内部错误
	const ERR_TOKEN_METHOD_PARA				= self::ERR_BASE - 7;				//	请求token,方法参数错误

	const ERR_TOKEN_SAVE_PARA_APP_ID		= self::ERR_BASE - 10;				//	保存token,appId参数验证失败
	const ERR_TOKEN_SAVE_PARA_TOKEN			= self::ERR_BASE - 11;				//	保存token,token参数验证失败
	const ERR_TOKEN_SAVE_PARA_FILE_TOKEN	= self::ERR_BASE - 12;				//	保存token,fileToken参数验证失败
	const ERR_TOKEN_SAVE_TOKEN_CHECK		= self::ERR_BASE - 13;				//	保存token,token验证失败

	const ERR_TOKEN_GET_PARA_ARR			= self::ERR_BASE - 20;				//	获得token,参数错误
	const ERR_TOKEN_GET_PARA_APP_ID			= self::ERR_BASE - 21;				//	获得token,appId参数错误

	const ERR_ADD_FILE_TO_OSS_PARA					= self::ERR_BASE - 30;				//	添加文件到阿里oss,参数错误

	const ERR_SAVE_FILE_TO_LOC_PARA_ARR				= self::ERR_BASE - 40;				//	用户上传文件保存到本地,参数错误
	const ERR_SAVE_FILE_TO_LOC_PARA_FIELD_NAME		= self::ERR_BASE - 41;				//	用户上传文件保存到本地,field参数错误
	const ERR_SAVE_FILE_TO_LOC_PARA_FILE_NAME		= self::ERR_BASE - 42;				//	用户上传文件保存到本地,file参数错误

	const ERR_CHECK_IMG_FILE_PARA_ARR				= self::ERR_BASE - 50;				//	验证用户上传的图片,参数列表错误
	const ERR_CHECK_IMG_FILE_PARA_MAX_SIZE			= self::ERR_BASE - 51;				//	验证用户上传的图片,上传最大限制参数错误
	const ERR_CHECK_IMG_FILE_PARA_FILE_PATH			= self::ERR_BASE - 52;				//	验证用户上传的图片,图片路径参数错误
	const ERR_CHECK_IMG_FILE_NOT_LEGAL_EXT			= self::ERR_BASE - 53;				//	验证用户上传的图片,图片格式非法
	const ERR_CHECK_IMG_FILE_OUT_MAXSIZE			= self::ERR_BASE - 54;				//	验证用户上传的图片,图片大小超限
	const ERR_CHECK_IMG_FILE_NOT_EXISTS				= self::ERR_BASE - 55;				//	验证用户上传的图片,图片路径不存在

	const ERR_GET_OSS_RTN_INFO_PARA_ARR				= self::ERR_BASE - 60;				//	获得oss返回信息,参数错误
	const ERR_GET_OSS_RTN_INFO_PARA_FILE_PATH		= self::ERR_BASE - 61;				//	获得oss返回信息,filePath参数错误
	const ERR_GET_OSS_RTN_INFO_PARA_DOMAIN			= self::ERR_BASE - 62;				//	获得oss返回信息,domain参数错误
	const ERR_GET_OSS_RTN_INFO_IMG_INFO				= self::ERR_BASE - 63;				//	获得oss返回信息,img info信息错误
	const ERR_GET_OSS_RTN_INFO_PARA_FILENAME		= self::ERR_BASE - 64;				//	获得oss返回信息,fileName参数错误
	const ERR_GET_OSS_RTN_INFO_EXT					= self::ERR_BASE - 65;				//	获得oss返回信息,获得文件扩展名失败

	const ERR_PUT_OSS_PARA_ARR						= self::ERR_BASE - 70;				//	上传oss,参数数组错误
	const ERR_PUT_OSS_PARA_TOKEN_ARR				= self::ERR_BASE - 71;				//	上传oss,token参数数组错误
	const ERR_PUT_OSS_PARA_TOKEN_ACCESS_ID			= self::ERR_BASE - 72;				//	上传oss,token参数数组,accessKeyId参数错误
	const ERR_PUT_OSS_PARA_TOKEN_ACCESS_SECRET		= self::ERR_BASE - 73;				//	上传oss,token参数数组,accessKeySecret参数错误
	const ERR_PUT_OSS_PARA_TOKEN_TMP_TOKEN			= self::ERR_BASE - 74;				//	上传oss,token参数数组,token参数错误
	const ERR_PUT_OSS_PARA_TIMEOUT					= self::ERR_BASE - 75;				//	上传oss,网络请求超时时间参数错误
	const ERR_PUT_OSS_PARA_FILE_PATH				= self::ERR_BASE - 76;				//	上传oss,上传文件路径参数错误
	const ERR_PUT_OSS_PARA_FILE_NAME				= self::ERR_BASE - 77;				//	上传oss,上传文件名参数错误
	const ERR_PUT_OSS_PARA_BUCKET					= self::ERR_BASE - 78;				//	上传oss,上传bucket位置
	const ERR_PUT_OSS_PARA_USE_INNER				= self::ERR_BASE - 79;				//	上传oss,是否使用内网标识参数错误
	const ERR_PUT_OSS_PARA_BUCKET_INNER_URL			= self::ERR_BASE - 80;				//	上传oss,bucket内网url参数错误
	const ERR_PUT_OSS_PARA_BUCKET_URL				= self::ERR_BASE - 81;				//	上传oss,bucket外网url参数错误
	const ERR_PUT_OSS_FAIL							= self::ERR_BASE - 82;				//	上传oss,上传oss失败
}

