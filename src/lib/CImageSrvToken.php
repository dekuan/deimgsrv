<?php

namespace dekuan\deimgsrv;


use dekuan\vdata\CRequest;

class CImageSrvToken {

	/**
	 * 获得token
	 * 检查本地是否存在有效token
	 * 如果存在,使用当前缓存的token
	 * 如果不存在,去图片服务器获得对应权限的新token并保存到本地
	 * $arrPara字段:
	 * <li> 'appid'		: [必须]需要获取token的appid,根据appid可以获得不同权限的token
	 * <li> 'url'		: [必须]获取token的服务器host
	 * <li> 'timeout'	: [非必须]请求超时时间,默认5秒
	 *
	 * @author wanganning
	 * @modify-log
	 *        name            date            reason
	 *        王安宁			2016-10-30			创建
	 *
	 * @param $arrPara		array		参数列表
	 * @param $arrRtn		array		返回的token
	 *
	 * @return int
	 */
	public static function getToken( $arrPara, & $arrRtn )
	{
		if ( ! is_array( $arrPara ) )
		{
			return CErrCode::ERR_TOKEN_GET_PARA_ARR;
		}

		if ( ! array_key_exists( 'appid', $arrPara ) || ! is_string( $arrPara[ 'appid' ] ) )
		{
			return CErrCode::ERR_TOKEN_GET_PARA_APP_ID;
		}

		$nErrCode = CErrCode::ERR_UNKNOWN;

		$sAppId = $arrPara[ 'appid' ];
		$arrFileToken = null;
		require_once dirname( __FILE__ ) . '/../token.php';

		if ( ! is_array( $arrFileToken ) || ! self::checkFileToken( $sAppId, $arrFileToken ) )
		{
			//	调用接口获得token
			$arrToken = [];
			$arrPara[ 'uri' ] = CConst::CONST_SRV_URI_TOKEN;
			$nErrCode = self::_getTokenFromSrv( $arrPara, $arrToken );

			if ( CErrCode::ERR_SUCC == $nErrCode )
			{
				//	保存token到本地
				$nErrCode = self::_saveFileToken( $sAppId, $arrToken, $arrFileToken );
				if ( CErrCode::ERR_SUCC == $nErrCode )
				{
					$arrRtn = $arrToken;
				}
			}
		}
		else
		{
			//	返回已经存在的token
			$arrRtn = $arrFileToken[ $sAppId ];
			$nErrCode = CErrCode::ERR_SUCC;
		}

		return $nErrCode;
	}


	/**
	 * 验证本地cookie是否存在及token是否失效
	 *
	 * @author wanganning
	 * @modify-log
	 *        name            date            reason
	 *        王安宁			2016-10-30			创建
	 *
	 * @param $sAppId		string	需要获取的token的appId
	 * @param $arrToken		array	需要验证的token数组
	 *
	 * @return bool
	 */
	public static function checkFileToken( $sAppId, $arrToken )
	{
		if ( ! is_array( $arrToken ) )
		{
			return false;
		}

		if ( ! is_string( $sAppId ) )
		{
			return false;
		}

		$bRtn = false;

		if ( array_key_exists( $sAppId, $arrToken ) )
		{
			$bRtn = self::checkToken( $arrToken[ $sAppId ] );
		}

		return $bRtn;
	}


	public static function checkToken( $arrToken )
	{
		if ( ! is_array( $arrToken ) )
		{
			return false;
		}

		return is_array( $arrToken )
		&& array_key_exists( CConst::CONST_ACCESS_KEY_ID, $arrToken )
		&& is_string( $arrToken[ CConst::CONST_ACCESS_KEY_ID ] )
		&& array_key_exists( CConst::CONST_ACCESS_KEY_SECRET, $arrToken )
		&& is_string( $arrToken[ CConst::CONST_ACCESS_KEY_SECRET ] )
		&& array_key_exists( CConst::CONST_TOKEN_EXPIRATION, $arrToken )
		&& is_numeric( $arrToken[ CConst::CONST_TOKEN_EXPIRATION ] )
		&& array_key_exists( CConst::CONST_TOKEN, $arrToken )
		&& is_string( $arrToken[ CConst::CONST_TOKEN ] )
		&& ( $arrToken[ CConst::CONST_TOKEN_EXPIRATION ] - time() >  CConst::CONST_EXPIRATION_MIN_TIME );
	}


	/**
	 * 从图片服务器获得token信息
	 * $arrPara中需要的参数信息:
	 * <li> 'url' 		: [必需]请求的图片服务host,
	 * <li> 'uri' 		: [必需]获得token的接口uri
	 * <li> 'timeout'	: [非必需]接口请求超时时间,默认5秒
	 * <li> 'appid'		: [必需]请求接口的appid,根据appid决定返回的token权限
	 *
	 * @author wanganning
	 * @modify-log
	 *        name            date            reason
	 *        王安宁			2016-10-28			创建
	 *
	 * @param     $arrPara		array		参数列表
	 * @param     $arrRtn		array		返回token信息
	 *
	 * @return int		错误码
	 */
	private static function _getTokenFromSrv( $arrPara, & $arrRtn )
	{
		if ( ! is_array( $arrPara ) )
		{
			return CErrCode::ERR_TOKEN_METHOD_PARA;
		}

		$sUrl = array_key_exists( 'url', $arrPara ) ? $arrPara[ 'url' ] : null;
		if ( ! is_string( $sUrl ) || strlen( $sUrl ) <= 0 )
		{
			return CErrCode::ERR_TOKEN_REQUEST_URL;
		}

		$sUri = array_key_exists( 'uri', $arrPara ) ? $arrPara[ 'uri' ] : null;
		if ( ! is_string( $arrPara[ 'uri' ] ) || strlen( $arrPara[ 'uri' ] ) <= 0 )
		{
			return CErrCode::ERR_TOKEN_REQUEST_URI;
		}

		$nTimeOut = array_key_exists( 'timeout', $arrPara ) ? $arrPara[ 'timeout' ] : 5;
		if ( ! is_numeric( $arrPara[ 'timeout' ] ) || $arrPara[ 'timeout' ] <= 0 )
		{
			return CErrCode::ERR_TOKEN_REQUEST_TIMEOUT;
		}

		$sAppID = array_key_exists( 'appid', $arrPara ) ? $arrPara[ 'appid' ] : null;
		if ( ! is_string( $sAppID ) || strlen( $sAppID ) <= 0 )
		{
			return CErrCode::ERR_TOKEN_REQUEST_APP_ID;
		}

		$nErrCode = CErrCode::ERR_UNKNOWN;

		$sReqUrl = $sUrl . $sUri;

		//	组装参数
		$arrPara = [
			'method' 	=> 'GET',
			'url'		=> $sReqUrl,
			'data'		=> [
				'appid' => $sAppID
			],
			'version'	=> '1.0',
			'timeout'	=> $nTimeOut
		];
		$arrResponse = [];
		$nErrCode = CRequest::GetInstance()->Http( $arrPara, $arrResponse );
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
					$nErrCode = CErrCode::ERR_TOKEN_REQUEST;
				}
			}
			else
			{
				$nErrCode = CErrCode::ERR_TOKEN_REQUEST_RESPONSE;
			}
		}
		else
		{
			$nErrCode = CErrCode::ERR_TOKEN_REQUEST_NETWORK;
		}

		return $nErrCode;
	}

	private function _saveFileToken( $sAppId, $arrToken, $arrFileToken )
	{
		if ( ! is_string( $sAppId ) )
		{
			return CErrCode::ERR_TOKEN_SAVE_PARA_APP_ID;
		}

		if ( ! is_array( $arrToken ) )
		{
			return CErrCode::ERR_TOKEN_SAVE_PARA_TOKEN;
		}

		if ( ! is_array( $arrFileToken ) )
		{
			return CErrCode::ERR_TOKEN_SAVE_PARA_FILE_TOKEN;
		}

		$nErrCode = CErrCode::ERR_UNKNOWN;

		if ( self::checkToken( $arrToken ) )
		{
			//	写入token文件中
			$sConfigName		= '$arrFileToken';
			$sAccessKeyId		= $arrToken[ 'AccessKeyId' ];
			$sAccessKeySecret	= $arrToken[ 'AccessKeySecret' ];
			$nExpiration		= $arrToken[ 'Expiration' ];
			$sSecurityToken		= $arrToken[ 'SecurityToken' ];

			$sContent         = <<<TOKENCONFIG
<?php
     $sConfigName =
     [
        'AccessKeyId'=>'$sAccessKeyId',
        'AccessKeySecret'=>'$sAccessKeySecret',
        'Expiration'=>'$nExpiration',
        'SecurityToken'=>'$sSecurityToken'
     ];
TOKENCONFIG;
			$rFileResource    = @fopen( dirname( __FILE__ ) . '/token.php', 'w+' );
			@fwrite( $rFileResource, $sContent );
			@fclose( $rFileResource );
			$nErrCode = CErrCode::ERR_SUCC;
		}
		else
		{
			$nErrCode = CErrCode::ERR_TOKEN_SAVE_TOKEN_CHECK;
		}

		return $nErrCode;
	}
}