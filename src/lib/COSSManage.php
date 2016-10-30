<?php
namespace dekuan\deimgsrv;

use OSS\OssClient;

class COSSManage {

	private $m_oImgFile;
	public function __construct( CImageFile $instance )
	{
		$this->m_oImgFile = $instance;
	}


	/**
	 * 上传文件到oss
	 * $arrToken参数信息
	 * <li> 'AccessKeyId'		: [必须]获取的token中accessKeyId
	 * <li> 'AccessKeySecret'	: [必须]获取的token中accessKeySecret
	 * <li> 'SecurityToken'		: [必须]获取的token
	 * $arrPara参数信息
	 * <li> 'timeout'			: [非必须]网络请求超时时间,默认5秒
	 * <li> 'filepath'			: [必须]上传文件的文件路径信息
	 * <li> 'filename'			: [非必须]上传文件的文件名,也做为oss对象名;不存在则从filepath中提取,不能为空字符串
	 * <li> 'bucket'			: [必须]要上传文件至阿里云指定的bucket名
	 * <li> 'useinner'			: [非必须]上传文件是否使用内网;CConst::CONST_USE_INNER使用内网,CConst::CONST_NOT_USE_INNER使用外网
	 * 								默认:CConst::CONST_DEFAULT_IF_USE_INNER;
	 * <li> 'bktinnerurl'		: [必须]要上传到的bucket内网地址
	 * <li> 'bkturl'			: [必须]要上传到的bucket外网地址
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
	 *        王安宁			2016-10-30			创建
	 *
	 * later 待添加功能
	 * <li> token有效期验证
	 * <li> bucket是否存在验证
	 *
	 * @param $arrPara		array		参数列表
	 * @param $arrToken		array		token信息
	 * @param $arrRtn		array		返回信息
	 *
	 * @return int		错误码
	 * @throws \OSS\Core\OssException
	 */
	public function uploadFile( $arrPara, $arrToken, & $arrRtn )
	{
		if ( ! is_array ( $arrPara ) )
		{
			return CErrCode::ERR_PUT_OSS_PARA_ARR;
		}

		if ( ! is_array( $arrToken ) )
		{
			return CErrCode::ERR_PUT_OSS_PARA_TOKEN_ARR;
		}

		$sAccessID = array_key_exists( 'AccessKeyId', $arrToken ) ? $arrToken[ 'AccessKeyId' ] : null;
		if ( ! is_string( $sAccessID ) || strlen( $sAccessID ) <= 0 )
		{
			return CErrCode::ERR_PUT_OSS_PARA_TOKEN_ACCESS_ID;
		}

		$sAccessKeySecret = array_key_exists( 'AccessKeySecret', $arrToken ) ? $arrToken[ 'AccessKeySecret' ] : null;
		if ( ! is_string( $sAccessKeySecret ) || strlen( $sAccessKeySecret ) <= 0 )
		{
			return CErrCode::ERR_PUT_OSS_PARA_TOKEN_ACCESS_SECRET;
		}

		$sToken = array_key_exists( 'SecurityToken', $arrToken ) ? $arrToken[ 'SecurityToken' ] : null;
		if ( ! is_string( $sToken ) || strlen( $sToken ) <= 0 )
		{
			return CErrCode::ERR_PUT_OSS_PARA_TOKEN_TMP_TOKEN;
		}

		$nTimeOut = array_key_exists( 'timeout', $arrPara ) ? $arrPara[ 'timeout' ] : 5;
		if ( ! is_numeric( $nTimeOut ) )
		{
			return CErrCode::ERR_PUT_OSS_PARA_TIMEOUT;
		}

		$sFilePath = array_key_exists( 'filepath', $arrPara ) ? $arrPara[ 'filepath' ] : null;
		if ( ! is_string( $sFilePath ) || strlen( $sFilePath ) <= 0 )
		{
			return CErrCode::ERR_PUT_OSS_PARA_FILE_PATH;
		}

		$sFileName = array_key_exists( 'filename', $arrPara ) ?
			$arrPara[ 'filename' ] : $this->m_oImgFile->getFileNameWithFilePath( $sFilePath );
		if ( ! is_string( $sFileName ) || strlen( $sFileName ) <= 0 )
		{
			return CErrCode::ERR_PUT_OSS_PARA_FILE_NAME;
		}

		$sBucketName = array_key_exists( 'bucket', $arrPara ) ?
			array_key_exists( 'bucket', $arrPara ) : CConst::CONST_IMG_BUCKET;
		if ( ! is_string( $sBucketName ) || strlen( $sBucketName ) <= 0 )
		{
			return CErrCode::ERR_PUT_OSS_PARA_BUCKET;
		}

		$nUseInner = array_key_exists( 'useinner', $arrPara ) ? $arrPara[ 'useinner' ] : CConst::CONST_DEFAULT_IF_USE_INNER;
		if ( ! is_numeric( $nUseInner ) )
		{
			return CErrCode::ERR_PUT_OSS_PARA_USE_INNER;
		}

		$sImgBktInnerUrl = array_key_exists( 'bktinnerurl', $arrPara ) ? $arrPara[ 'bktinnerurl' ] : null;
		if ( ! is_string( $sImgBktInnerUrl ) || strlen( $sImgBktInnerUrl ) <= 0 )
		{
			return CErrCode::ERR_PUT_OSS_PARA_BUCKET_INNER_URL;
		}

		$sImgBktUrl = array_key_exists( 'bkturl', $arrPara ) ? $arrPara[ 'bkturl' ] : null;
		if ( ! is_string( $sImgBktUrl ) || strlen( $sImgBktUrl ) <= 0 )
		{
			return CErrCode::ERR_PUT_OSS_PARA_BUCKET_URL;
		}

		$nErrCode = CErrCode::ERR_UNKNOWN;

		$sEndPoint = '';
		if ( CConst::CONST_USE_INNER == $nUseInner )
		{
			$sEndPoint = $sImgBktInnerUrl;
		}
		else
		{
			$sEndPoint = $sImgBktUrl;
		}

		//	验证上传内容
		$nErrCode = $this->m_oImgFile->checkFile( $arrPara );
		if ( CErrCode::ERR_SUCC == $nErrCode )
		{
			//	上传oss
			$OssClient    = new OssClient( $sAccessID, $sAccessKeySecret, $sEndPoint, $isCName = false, $sToken );
			$OssClient->setTimeout( $nTimeOut );
			$result    = @$OssClient->uploadFile( $sBucketName, $sFileName, $sFilePath );
			if ( is_null( $result ) && ( 0 !== $result ) )
			{
				//	返回上传后文件信息
				$nErrCode = $this->m_oImgFile->getOssRtnInfo( $arrPara, $arrRtn );
			}
			else
			{
				$nErrCode = CErrCode::ERR_PUT_OSS_FAIL;
			}
		}


		return $nErrCode;
	}
}
