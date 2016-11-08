<?php
namespace dekuan\deimgsrv\lib;

use dekuan\defileup\CFileUploader;

class CImageFile {

	static $m_ArrExtFImgType = [		//	上传文件类型数组
		IMAGETYPE_JPEG,
		IMAGETYPE_PNG,
		IMAGETYPE_BMP
	];

	static $m_ArrExtFImgExt = [
		IMAGETYPE_BMP => 'bmp',
		IMAGETYPE_JPEG => 'jpg',
		IMAGETYPE_PNG => 'png'
	];

	static $m_DefaultMaxFileSize = 2097152;		//	单位B;默认最大文件限制:2MB,=2 * 1024 * 1024B,


	/**
	 * 保存文件到本地临时目录
	 * $arrPara参数字段
	 * <li> 'fieldname'	: [必须]文件名参数字段名,$_FILES[$fieldname] or $_GET[$fieldname]
	 * <li> 'filename'    : [非必须]上传保存到oss的文件名;
	 * 						如果该参数存在,则使用指定的文件名;  *警告:存在文件名冲突的情况,采用覆盖的方式
	 * 						不存在该参数的话,会随机生成一个扩展名为jpg的文件名
	 *
	 * @author wanganning
	 * @modify-log
	 *        name            date            reason
	 *        王安宁			2016-10-30			创建
	 *
	 * @param $arrPara		array		参数表
	 *
	 * @return int		错误码
	 */
	public static function saveFileToTmpDir ( & $arrPara )
	{
		if ( ! is_array( $arrPara ) )
		{
			return CErrCode::ERR_SAVE_FILE_TO_LOC_PARA_ARR;
		}

		$sFieldName = array_key_exists( 'fieldname', $arrPara ) ? $arrPara[ 'fieldname' ] : CConst::CONST_FILE_UP_NAME;
		if ( ! is_string( $sFieldName ) || strlen( $sFieldName ) <= 0 )
		{
			return CErrCode::ERR_SAVE_FILE_TO_LOC_PARA_FIELD_NAME;
		}

		$sFileName = array_key_exists( 'filename', $arrPara) ? $arrPara[ 'filename' ] : self::_getTmpFileName();
		if ( ! is_string( $sFileName ) || strlen( $sFileName ) <= 0 )
		{
			return CErrCode::ERR_SAVE_FILE_TO_LOC_PARA_FILE_NAME;
		}

		$nErrCode = CErrCode::ERR_UNKNOWN;

		$cUpf = new CFileUploader();
		$cUpf->setFieldName( $sFieldName );

		$sTmpDir = CConst::CONST_FILE_UP_TMP_DIR;
		if ( ! is_dir( $sTmpDir ) ) {
			mkdir( $sTmpDir, 0777 );
			chmod( $sTmpDir, 0755 );
		}

		$sPath = $sTmpDir . $sFileName;
		$nErrCode = $cUpf->saveUploadFile( $sPath );
		if( CErrCode::ERR_SUCC == $nErrCode ) {
			$arrPara[ 'filepath' ] = $sPath;
			$arrPara[ 'filename' ] = $sFileName;
		}

		return $nErrCode;
	}


	/**
	 * 验证上传的oss文件是否合法
	 * $arrPara参数字段:
	 * <li> 'maxsize'	: [非必须]文件大小最大限制,默认2M
	 * <li> 'filepath'	: [必须]上传文件路径
	 *
	 * @author wanganning
	 * @modify-log
	 *        name            date            reason
	 *        王安宁			2016-10-30			创建
	 *
	 * @param $arrPara		array		参数列表
	 *
	 * @return int		错误码
	 */
	public static function checkFile( $arrPara )
	{
		if ( ! is_array( $arrPara ) )
		{
			return CErrCode::ERR_CHECK_IMG_FILE_PARA_ARR;
		}

		$nMaxFileSize = array_key_exists( 'maxsize', $arrPara ) ? $arrPara[ 'maxsize' ] : self::$m_DefaultMaxFileSize;
		if( ! is_numeric( $nMaxFileSize ) )
		{
			return CErrCode::ERR_CHECK_IMG_FILE_PARA_MAX_SIZE;
		}

		$sFilePath = array_key_exists( 'filepath', $arrPara ) ? $arrPara[ 'filepath' ] : null;
		if ( ! is_string( $sFilePath ) || strlen( $sFilePath ) <= 0 )
		{
			return CErrCode::ERR_CHECK_IMG_FILE_PARA_FILE_PATH;
		}

		$nErrCode = CErrCode::ERR_UNKNOWN;

		//	验证文件是否存在
		if ( file_exists( $sFilePath ) )
		{
			//	验证文件大小是否超限
			if ( filesize( $sFilePath ) <= $nMaxFileSize )
			{
				//	验证文件格式
				$nImgType = exif_imagetype( $sFilePath );
				if ( in_array( $nImgType, self::$m_ArrExtFImgType ) )
				{
					$nErrCode = CErrCode::ERR_SUCC;
				}
				else
				{
					//	格式非法
					$nErrCode = CErrCode::ERR_CHECK_IMG_FILE_NOT_LEGAL_EXT;
				}
			}
			else
			{
				//	文件大小超限
				$nErrCode = CErrCode::ERR_CHECK_IMG_FILE_OUT_MAXSIZE;
			}
		}
		else
		{
			//	文件不存在
			$nErrCode = CErrCode::ERR_CHECK_IMG_FILE_NOT_EXISTS;
		}

		return $nErrCode;
	}

	public function getFileNameWithFilePath( $sFilePath )
	{
		if ( ! is_string( $sFilePath ) || strlen( $sFilePath ) <= 0 )
		{
			return '';
		}

		$sFileName = '';
		$arrPathInfo = @pathinfo( $sFilePath );

		if ( is_array( $arrPathInfo )
			&& array_key_exists( 'basename', $arrPathInfo )
		)
		{
			$sFileName = $arrPathInfo[ 'basename' ];
		}

		return $sFileName;
	}


	/**
	 * 获得上传oss的图片内容信息
	 * $arrPara中参数信息
	 * <li> 'filepath'	: [必须]上传文件路径
	 * <li> 'domain'	: [必须]bucket访问自定义url
	 * <li> 'filename'	: [非必须]上传文件文件名,也作为oss对象名;不存在则从filepath中提取,不能为空字符串
	 * $arrRtn返回信息
	 * <li> 'imgid'		: 上传的oss对象名
	 * <li> 'ext'		: 上传图片的扩展名
	 * <li> 'imgurl'	: 上传的图片oss访问地址
	 * <li> 'width'		: 上传的图片宽度
	 * <li> 'height'	: 上传的图片宽度
	 * <li> 'mime'		: 上传图片的mime信息
	 *
	 * @author wanganning
	 * @modify-log
	 *        name            date            reason
	 *        王安宁			2016-10-30			创建
	 *
	 * @param $arrPara		array		参数列表
	 * @param $arrRtn		array		返回信息
	 *
	 * @return int
	 */
	public function getOssRtnInfo( $arrPara, & $arrRtn )
	{
		if ( ! is_array( $arrPara ) )
		{
			return CErrCode::ERR_GET_OSS_RTN_INFO_PARA_ARR;
		}

		$sFilePath = array_key_exists( 'filepath', $arrPara ) ? $arrPara[ 'filepath' ] : null;
		if ( ! is_string( $sFilePath ) || strlen( $sFilePath ) <= 0 )
		{
			return CErrCode::ERR_GET_OSS_RTN_INFO_PARA_FILE_PATH;
		}

		$sDomain = array_key_exists( 'domain', $arrPara ) ? $arrPara[ 'domain' ] : null;
		if ( ! is_string( $sDomain ) || strlen( $sDomain ) <= 0 )
		{
			return CErrCode::ERR_GET_OSS_RTN_INFO_PARA_DOMAIN;
		}

		$sFileName = array_key_exists( 'filename', $arrPara ) ?
			$arrPara[ 'filename' ] : self::getFileNameWithFilePath( $sFilePath );
		if ( ! is_string( $sFileName ) || strlen( $sFileName ) <= 0 )
		{
			return CErrCode::ERR_GET_OSS_RTN_INFO_PARA_FILENAME;
		}

		$nErrCode = CErrCode::ERR_UNKNOWN;

		$sExt = self::getFileExtWithFileName( $sFilePath );

		if ( is_string( $sExt ) && strlen( $sExt ) > 0 )
		{
			$arrImgInfo = @getimagesize( $sFilePath );
			if( is_array( $arrImgInfo )
				&& array_key_exists( '0', $arrImgInfo )		//	图像宽度
				&& array_key_exists( '1', $arrImgInfo )		//	图像高度
				&& array_key_exists( 'mime', $arrImgInfo )	//	图像的MIME信息
			)
			{
				$nErrCode = CErrCode::ERR_SUCC;
				$arrRtn	= [
					'imgid'		=> $sFileName,
					'ext'		=> "." . $sExt,
					'imgurl'	=> $sDomain . '/' . $sFileName,
					'width' 	=> $arrImgInfo[ 0 ],
					'height'	=> $arrImgInfo[ 1 ],
					'mime' 		=> $arrImgInfo[ 'mime' ],
				];
			}
			else
			{
				$nErrCode = CErrCode::ERR_GET_OSS_RTN_INFO_IMG_INFO;
			}
		}
		else
		{
			$nErrCode = CErrCode::ERR_GET_OSS_RTN_INFO_EXT;
		}

		return $nErrCode;
	}

	public static function getFileExtWithFileName( $sFilePath )
	{
		if ( ! is_string( $sFilePath ) || strlen( $sFilePath ) < 0 )
		{
			return '';
		}

		$sExt = '';
		if ( file_exists( $sFilePath ) )
		{
			$arrPathInfo = pathinfo( $sFilePath );
			if ( is_array( $arrPathInfo ) )
			{
				if ( array_key_exists( 'extension', $arrPathInfo ) )
				{
					if ( is_string( $arrPathInfo[ 'extension' ] )
						&& strlen( $arrPathInfo[ 'extension' ] ) > 0
					)
					{
						$sExt = $arrPathInfo[ 'extension' ];
					}
					else
					{
						$nImgType = exif_imagetype( $sFilePath );
						if ( array_key_exists( $nImgType, self::$m_ArrExtFImgExt ) )
						{
							$sExt = self::$m_ArrExtFImgExt[ $nImgType ];
						}
					}
				}
			}
		}

		return $sExt;
	}

	public static function getDefaultFileName( $sFilePath )
	{
		return self::_getTmpFileName();
	}


	private static function _getTmpFileName()
	{
		$sBaseName = md5( microtime( true )  . rand( 10000, 99999 ) );
		$sExtension = '.jpg';
		$sFileName = $sBaseName . $sExtension;

		return $sFileName;
	}
}
