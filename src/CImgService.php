<?php

namespace dekuan\deimgsrv;

class CImgService {
	public function __construct()
	{
	}


	/**
	 * 添加文件到阿里oss
	 * $arrPara参数信息
	 * <li> 'appid'				: [必须]需要获取token的appid,根据appid可以获得不同权限的token
	 * <li> 'url'				: [必须]获取token的服务器host
	 * <li> 'timeout'			: [非必须]请求超时时间,默认5秒
	 * <li> 'fieldname'			: [必须]文件名参数字段名,$_FILES[$fieldname] or $_GET[$fieldname]
	 * <li> 'filename'    		: [非必须]上传保存到oss的文件名;
	 * 								如果该参数存在,则使用指定的文件名;  *警告:存在文件名冲突的情况,采用覆盖的方式
	 * 								不存在该参数的话,会随机生成一个扩展名为jpg的文件名做为文件名
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
	 *        王安宁
	 *
	 * @param $arrPara		array		参数列表
	 * @param $arrRtn		array		返回列表
	 *
	 * @return int
	 */
	public function addFileToOss( $arrPara, $arrRtn )
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
}

