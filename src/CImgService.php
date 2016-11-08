<?php

namespace dekuan\deimgsrv;

use dekuan\vdata\CRequest;
use dekuan\deimgsrv\lib\CDeImgConst;
use dekuan\deimgsrv\lib\CErrCode;
use dekuan\deimgsrv\lib\CImageFile;
use dekuan\deimgsrv\lib\COSSManage;
use dekuan\deimgsrv\lib\CImageSrvToken;

class CImgService {
	public function __construct()
	{
	}


	/**
	 * 添加文件到阿里oss
	 * $arrPara参数信息
	 * <li> 'appid'				: [必须]需要获取token的appid,根据appid可以获得不同权限的token
	 * <li> 'security'			: [必须]需要获取token的appid对应的安全验证码,由imgSrv管理
	 * <li> 'fieldname'			: [必须]文件名参数字段名,$_FILES[$fieldname] or $_GET[$fieldname]
	 * <li> 'bucket'			: [必须]要上传文件至阿里云指定的bucket名
	 * <li> 'bktinnerurl'		: [必须]要上传到的bucket内网地址
	 * <li> 'bkturl'			: [必须]要上传到的bucket外网地址
	 * <li> 'url'				: [非必须]获取token的服务器host,默认img.dekuan.org
	 * <li> 'timeout'			: [非必须]请求超时时间,默认5秒
	 * <li> 'filename'    		: [非必须]上传保存到oss的文件名;
	 * 								如果该参数存在,则使用指定的文件名;  *警告:存在文件名冲突的情况,采用覆盖的方式
	 * 								不存在该参数的话,会随机生成一个扩展名为jpg的文件名做为文件名
	 * <li> 'filepath'			: [非必须]文件在服务器的绝对路径,若该参数为空,则会从表单中尝试下载图片到服务器.
	 * <li> 'useinner'			: [非必须]上传文件是否使用内网;CConst::CONST_USE_INNER使用内网,CConst::CONST_NOT_USE_INNER使用外网,默认使用内网
	 *
	 * $arrRtn返回信息
	 * <li> 'imgid'				: 上传的oss对象名
	 * <li> 'ext'				: 上传图片的扩展名
	 * <li> 'imgurl'			: 上传的图片oss访问地址
	 * <li> 'width'				: 上传的图片宽度
	 * <li> 'height'			: 上传的图片宽度
	 * <li> 'mime'				: 上传图片的mime信息
	 *
	 * @author wanganning
	 * @modify-log
	 *        name            date            reason
	 *        王安宁
	 *
	 * @param $arrPara		array		参数列表
	 * @param $arrRtn		array		返回列表
	 *
	 * @return int
	 */
	public static function addFileToOss( $arrPara, & $arrRtn )
	{
		if ( ! is_array( $arrPara ) )
		{
			return CErrCode::ERR_ADD_FILE_TO_OSS_PARA;
		}

		$nErrCode = CErrCode::ERR_UNKNOWN;

		//	获得token
		$arrToken = [];
		$nErrCode = CImageSrvToken::getToken( $arrPara, $arrToken );
		if ( CErrCode::ERR_SUCC == $nErrCode )
		{
			if ( ! array_key_exists( 'filepath', $arrPara ) || ! is_string( $arrPara[ 'filepath' ] ) )
			{
				//	保存上传文件到临时目录
				$nErrCode = CImageFile::saveFileToTmpDir( $arrPara );
			}

			if ( CErrCode::ERR_SUCC == $nErrCode )
			{
				//	上传到oss
				$oImgFile = new CImageFile();
				$oOSSMag = new COSSManage( $oImgFile );
				$nErrCode = $oOSSMag->uploadFile( $arrPara, $arrToken, $arrRtn );
			}
		}

		return $nErrCode;
	}


	/**
	 * 通过图片url传到oss
	 * $arrPara参数:
	 * <li> 'appid'			: [必须]请求接口appid
	 * <li> 'security'		: [必须]请求接口appid对应验证码
	 * <li> 'imgurl'		: [必须]上传的图片url
	 * <li> 'filename'		: [非必须]上传至oss后对应的对象名,如果该参数为空,则随机生成filename
	 * <li> 'timeout'		: [非必须]请求服务器上传超时时间,默认5秒
	 * <li> 'url'			: [非必须]请求图片服务器url,默认img.dekuan.org
	 * <li> 'uri'			: [非必须]请求图片服务器接口,默认'/upimgbyurl'
	 *
	 * $arrRtn返回信息
	 * <li> 'imgid'			: 上传的oss对象名
	 * <li> 'ext'			: 上传图片的扩展名
	 * <li> 'imgurl'		: 上传的图片oss访问地址
	 * <li> 'width'			: 上传的图片宽度
	 * <li> 'height'		: 上传的图片宽度
	 * <li> 'mime'			: 上传图片的mime信息
	 *
	 * @author wanganning
	 * @modify-log
	 *        name            date            reason
	 *        王安宁			2016-11-07			创建
	 *
	 * @param $arrPara		array		参数列表
	 * @param $arrRtn		array		返回列表
	 *
	 * @return int
	 */
	public static function addUrlToOSS ( $arrPara, & $arrRtn )
	{
		if ( ! is_array( $arrPara ) || count( $arrPara ) <= 0 )
		{
			return CErrCode::ERR_IMG_URL_TO_OSS_PARA_ARR;
		}

		$sAppID = array_key_exists( 'appid', $arrPara ) ? $arrPara[ 'appid' ] : null;
		if ( ! is_string( $sAppID ) || strlen( $sAppID ) <=0 )
		{
			return CErrCode::ERR_IMG_URL_TO_OSS_PARA_APP_ID;
		}

		$sSecurity = array_key_exists( 'security', $arrPara ) ? $arrPara[ 'security' ] : null;
		if ( ! is_string( $sSecurity ) || strlen( $sSecurity ) <= 0 )
		{
			return CErrCode::ERR_IMG_URL_TO_OSS_PARA_APP_SECURITY;
		}

		$sImgUrl = array_key_exists( 'imgurl', $arrPara ) ? $arrPara[ 'imgurl' ] : null;
		if ( ! is_string( $sImgUrl ) || strlen( $sImgUrl ) <= 0 )
		{
			return CErrCode::ERR_IMG_URL_TO_OSS_PARA_IMG_URL;
		}

		$nTimeOut = array_key_exists( 'timeout', $arrPara ) ? $arrPara[ 'timeout' ] : CDeImgConst::CONST_REQUEST_TIMEOUT_DEFAULT;
		if ( ! is_numeric( $nTimeOut ) || $nTimeOut <= 0 )
		{
			return CErrCode::ERR_IMG_URL_TO_OSS_PARA_TIME_OUT;
		}

		$sUrl = array_key_exists( 'url', $arrPara ) ? $arrPara[ 'url' ] : CDeImgConst::URL_IMG_CENTER;
		if ( ! is_string( $sUrl ) || strlen( $sUrl ) <= 0 )
		{
			return CErrCode::ERR_IMG_URL_TO_OSS_PARA_CENTER_URL;
		}

		$sUri = array_key_exists( 'uri', $arrPara ) ? $arrPara[ 'uri' ] : CDeImgConst::CONST_SRV_URI_IMG_URL_TO_OSS;
		if ( ! is_string( $sUri ) || strlen( $sUri ) <= 0 )
		{
			return CErrCode::ERR_IMG_URL_TO_OSS_PARA_URI;
		}

		$sFileName = array_key_exists( 'filename', $arrPara ) ? $arrPara[ 'filename' ] : null;
		if ( ! is_null( $sFileName ) && ( ! is_string( $sFileName ) || strlen( $sFileName ) <= 0 ) )
		{
			return CErrCode::ERR_IMG_URL_TO_OSS_PARA_FILE_NAME;
		}

		$nErrCode = CErrCode::ERROR_UNKNOWN;

		$sRequestUrl = $sUrl . $sUri;

		$arrData = [
			'appid'		=> $sAppID,
			'security'	=> $sSecurity,
			'imgurl'	=> $sImgUrl
		];
		if ( ! is_null( $sFileName ) )
		{
			$arrData[ 'filename' ] = $sFileName;
		}

		$arrInput = [
			'method'	=> 'GET',
			'url'		=> $sRequestUrl,
			'data'		=> $arrData,
			'timeout'	=> $nTimeOut
		];

		$arrResponse = [];
		$nErrCode = CRequest::GetInstance()->Get( $arrInput, $arrResponse );

		if ( CErrCode::ERR_SUCC == $nErrCode )
		{
			if ( array_key_exists( 'errorid', $arrResponse )
				&& array_key_exists( 'vdata', $arrResponse )
			)
			{
				$nErrCode = $arrResponse[ 'errorid' ];
				if ( CErrCode::ERR_SUCC == $nErrCode )
				{
					$arrRtn = $arrResponse[ 'vdata' ];
					$nErrCode = CErrCode::ERR_SUCC;
				}
				else
				{
					$nErrCode = CErrCode::ERR_IMG_URL_TO_OSS;
				}
			}
			else
			{
				$nErrCode = CErrCode::ERR_IMG_URL_TO_OSS_RESPONSE;
			}
		}
		else
		{
			$nErrCode = CErrCode::ERR_IMG_URL_TO_OSS_REQUEST_NETWORK;
		}

		return $nErrCode;
	}


	/**
	 * 通过给定file名,展示图片
	 * <li> 'appid'			: [必需]请求appId参数
	 * <li> 'security'		: [必需]对应appId安全校验码
	 * <li> 'filename'		: [必需]需要获取的图片名
	 * <li> 'timeout'		: [非必需]请求超时时间,默认5秒
	 * <li> 'url'			: [非必需]请求图片服务器url,默认:img.dekuan.org
	 * <li> 'uri'			: [非必需]请求图片服务接口uri,默认:/showimg
	 *
	 * @author wanganning
	 * @modify-log
	 *        name            date            reason
	 *        王安宁			2016-11-08			创建
	 *
	 * @param $arrPara		array		参数列表
	 * @param $arrRtn		array		返回
	 *
	 * @return int
	 */
	public static function ShowPicture( $arrPara, & $arrRtn )
	{
		if ( ! is_array( $arrPara ) || count( $arrPara ) <= 0 )
		{
			return CErrCode::ERR_SHOW_IMG_WITH_NAME_PARA_ARR;
		}

		$sAppID = array_key_exists( 'appid', $arrPara ) ? $arrPara[ 'appid' ] : null;
		if ( ! is_string( $sAppID ) || strlen( $sAppID ) <= 0 )
		{
			return CErrCode::ERR_SHOW_IMG_WITH_NAME_PARA_APP_ID;
		}

		$sSecurity = array_key_exists( 'security', $arrPara ) ? $arrPara[ 'security' ] : null;
		if ( ! is_string( $sSecurity ) || strlen( $sSecurity ) <= 0 )
		{
			return CErrCode::ERR_SHOW_IMG_WITH_NAME_PARA_APP_SECURITY;
		}

		$sFileName = array_key_exists( 'filename', $arrPara ) ? $arrPara[ 'filename' ] : null;
		if ( ! is_string( $sFileName ) || strlen( $sFileName ) <= 0 )
		{
			return CErrCode::ERR_SHOW_IMG_WITH_NAME_PARA_FILE_NAME;
		}

		$nTimeOut = array_key_exists( 'timeout', $arrPara ) ? $arrPara[ 'timeout' ] : CDeImgConst::CONST_REQUEST_TIMEOUT_DEFAULT;
		if ( ! is_numeric( $nTimeOut ) || $nTimeOut <= 0 )
		{
			return CErrCode::ERR_SHOW_IMG_WITH_NAME_PARA_TIME_OUT;
		}

		$sUrl = array_key_exists( 'url', $arrPara ) ? $arrPara[ 'url' ] : CDeImgConst::URL_IMG_CENTER;
		if ( ! is_string( $sUrl ) || strlen( $sUrl ) <= 0 )
		{
			return CErrCode::ERR_SHOW_IMG_WITH_NAME_PARA_URL;
		}

		$sUri = array_key_exists( 'uri', $arrPara ) ? $arrPara[ 'uri' ] : CDeImgConst::CONST_SRV_URI_IMG_URL;
		if ( ! is_string( $sUri ) || strlen( $sUri ) <= 0 )
		{
			return CErrCode::ERR_SHOW_IMG_WITH_NAME_PARA_URI;
		}

		$sRequestUrl = $sUrl . $sUri;
		$arrParam    = [
			'appid'		=> $sAppID,
			'security'	=> $sSecurity,
			'img_name' 	=> $sFileName,
		];

		$arrInput = [
			'method' 	=> 'GET',
			'url'		=> $sRequestUrl,
			'data'		=> $arrParam,
			'timeout'	=> $nTimeOut
		];

		$arrResponse = [];
		$nErrCode = CRequest::GetInstance()->HttpRaw( $arrInput, $arrResponse );

		if ( CErrCode::ERR_SUCC == $nErrCode )
		{
			if ( array_key_exists( 'data', $arrResponse ) && is_string( $arrResponse[ 'data' ] ) )
			{
				$arrRes = @json_decode( $arrResponse[ 'data' ], true );
				if ( is_array( $arrRes ) )
				{
					if ( array_key_exists( 'errorid', $arrRes )
						&& array_key_exists( 'vdata', $arrRes )
					)
					{
						$nErrCode = $arrRes[ 'errorid' ];
						if ( CErrCode::ERR_SUCC == $nErrCode )
						{
							$arrRtn = $arrRes[ 'vdata' ];
							$nErrCode = CErrCode::ERR_SUCC;
						}
						else
						{
							$nErrCode = CErrCode::ERR_SHOW_IMG_WITH_NAME;
						}
					}
					else
					{
						$nErrCode = CErrCode::ERR_SHOW_IMG_WITH_NAME_RESPONSE;
					}
				}
				else
				{
					$nErrCode = CErrCode::ERR_SHOW_IMG_WITH_NAME_RES_DATA_ARR;
				}
			}
			else
			{
				$nErrCode = CErrCode::ERR_SHOW_IMG_WITH_NAME_RESPONSE_DATA;
			}
		}
		else if ( array_key_exists( 'status', $arrResponse ) && 301 == $arrResponse[ 'status' ] )
		{
			$nErrCode = CErrCode::ERROR_SUCCESS;
		}
		else
		{
			$nErrCode = CErrCode::ERR_SHOW_IMG_WITH_NAME_REQUEST_NETWORK;
		}

		return $nErrCode;
	}

}

