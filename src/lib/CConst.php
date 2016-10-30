<?php 
namespace dekuan\deimgsrv;

class CConst
{

	const CONST_ACCESS_KEY_ID = 'AccessKeyId';            	//	accessKey对应key值
	const CONST_ACCESS_KEY_SECRET = 'AccessKeySecret';      //	accessKeySecret对应key值
	const CONST_TOKEN_EXPIRATION = 'Expiration';            //	token有效期对应key值
	const CONST_TOKEN = 'SecurityToken';            		//	token对应key值

	const CONST_EXPIRATION_MIN_TIME = 100;					//	token剩余最小有效时间

	const CONST_SRV_URI_TOKEN = '/gettoken';				//	img服务器获得token uri

	const CONST_FILE_UP_NAME = 'dkfile';					//	上传文件,input name
	const CONST_FILE_UP_TMP_DIR = 'pictures/';				//	临时文件目录地址
	const CONST_IMG_BUCKET = 'deimage';						//	image bucket名字

	const CONST_DEFAULT_IF_USE_INNER = self::CONST_USE_INNER;	//	是否使用内网
	const CONST_NOT_USE_INNER = 0;								//	使用外网标识
	CONST CONST_USE_INNER = 1;									//	使用内网标识
}
