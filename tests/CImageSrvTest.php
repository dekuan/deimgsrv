<?php 

define( 'TEST_SKIP', 1 );

require_once( __DIR__ . '/TestBase.php' );

require_once( __DIR__ . '/../vendor/dekuan/vdata/autoload.php' );
require_once( __DIR__ . '/../vendor/dekuan/defileup/autoload.php' );
require_once( __DIR__ . '/../vendor/dekuan/delib/autoload.php' );
require_once( __DIR__ . '/../autoload.php' );



class CImageSrvTest extends TestBase
{
	public function testAddFileToOss()
	{
		$this->skipTest( TEST_SKIP & 0 );
		$sTestName = '添加图片到Oss测试';

		$arrExcept = [
			'imgid' 	=> 'wantest',
			'ext'		=> '.jpg',
			'imgurl'	=> 'http://deimage.dekuan.org/wantest',
			'width'		=> 375,
			'height'	=> 220,
			'mime'		=> 'image/jpeg'
		];

		$arrConfig = $this->_getConfig();
		$arrConfig[ 'filename' ] = 'wantest';
		$arrConfig[ 'filepath' ] = __DIR__ . '/41231r51312.jpg';
		$arrRtn = [];
		$nErrCode = \dekuan\deimgsrv\CImgService::addFileToOss( $arrConfig, $arrRtn );

		if ( \dekuan\deimgsrv\lib\CErrCode::ERROR_SUCCESS == $nErrCode )
		{
			$bExcept = $this->mySeeJson( $arrRtn, $arrExcept );
			if ( true == $bExcept )
			{
				$this->echoTestResult( true, $sTestName );
			}
			else
			{
				$this->echoTestResult( false, $sTestName, '返回结果验证失败;', $arrExcept, $arrRtn );
			}
		}
		else
		{
			$this->echoTestResult( false, $sTestName, '错误码:' . $nErrCode );
		}
	}

	public function testAddFileToOssWithAnotherID()
	{
		$this->skipTest( TEST_SKIP & 0 );
		$sTestName = '添加图片到Oss测试';

		$arrExcept = [
			'imgid' 	=> 'wantest1',
			'ext'		=> '.jpg',
			'imgurl'	=> 'http://deimage.dekuan.org/wantest1',
			'width'		=> 375,
			'height'	=> 220,
			'mime'		=> 'image/jpeg'
		];

		$arrConfig = $this->_getAnotherConfig();
		$arrConfig[ 'filename' ] = 'wantest1';
		$arrConfig[ 'filepath' ] = __DIR__ . '/41231r51312.jpg';
		$arrRtn = [];
		$nErrCode = \dekuan\deimgsrv\CImgService::addFileToOss( $arrConfig, $arrRtn );

		if ( \dekuan\deimgsrv\lib\CErrCode::ERROR_SUCCESS == $nErrCode )
		{
			$bExcept = $this->mySeeJson( $arrRtn, $arrExcept );
			if ( true == $bExcept )
			{
				$this->echoTestResult( true, $sTestName );
			}
			else
			{
				$this->echoTestResult( false, $sTestName, '返回结果验证失败;', $arrExcept, $arrRtn );
			}
		}
		else
		{
			$this->echoTestResult( false, $sTestName, '错误码:' . $nErrCode );
		}
	}


	public function testAddFileToOssWithNoFileName ()
	{
		$this->skipTest( TEST_SKIP );
		$sTestName = '添加图片到Oss不指定fileName测试';

		$arrExcept = [
			'imgid' 	=> '@SKIP@',
			'ext'		=> '.jpg',
			'imgurl'	=> '@SKIP@',
			'width'		=> 375,
			'height'	=> 220,
			'mime'		=> 'image/jpeg'
		];

		$arrConfig = $this->_getConfig();
		$arrConfig[ 'filepath' ] = __DIR__ . '/41231r51312.jpg';
		$arrRtn = [];
		$nErrCode = \dekuan\deimgsrv\CImgService::addFileToOss( $arrConfig, $arrRtn );

		if ( \dekuan\deimgsrv\lib\CErrCode::ERROR_SUCCESS == $nErrCode )
		{
			$bExcept = $this->mySeeJson( $arrRtn, $arrExcept );
			if ( true == $bExcept )
			{
				$this->echoTestResult( true, $sTestName );
			}
			else
			{
				$this->echoTestResult( false, $sTestName, '返回结果验证失败;', $arrExcept, $arrRtn );
			}
		}
		else
		{
			$this->echoTestResult( false, $sTestName, '错误码:' . $nErrCode );
		}
	}

	public function testAddUrlToOSS()
	{
		$this->skipTest( TEST_SKIP );
		$sTestName = '通过url添加图片到Oss测试';

		$arrExcept = [
			'imgid' 	=> '@SKIP@',
			'ext'		=> '.jpg',
			'imgurl'	=> '@SKIP@',
			'width'		=> 598,
			'height'	=> 425,
			'mime'		=> 'image/png'
		];

		$arrConfig = $this->_getConfig();
		$arrConfig[ 'imgurl' ] = 'http://ofstpx613.bkt.clouddn.com/F85D.tmp.jpg';
		$arrRtn = [];
		$nErrCode = \dekuan\deimgsrv\CImgService::addUrlToOSS( $arrConfig, $arrRtn );

		if ( \dekuan\deimgsrv\lib\CErrCode::ERROR_SUCCESS == $nErrCode )
		{
			$bExcept = $this->mySeeJson( $arrRtn, $arrExcept );
			if ( true == $bExcept )
			{
				$this->echoTestResult( true, $sTestName );
			}
			else
			{
				$this->echoTestResult( false, $sTestName, '返回结果验证失败;', $arrExcept, $arrRtn );
			}
		}
		else
		{
			$this->echoTestResult( false, $sTestName, '错误码:' . $nErrCode );
		}
	}


	public function testAddUrlToOSSWithFileName()
	{
		$this->skipTest( TEST_SKIP );
		$sTestName = '通过url添加图片到Oss测试';

		$arrExcept = [
			'imgid' 	=> 'wantesturlimg.jpg',
			'ext'		=> '.jpg',
			'imgurl'	=> 'http://deimage.dekuan.org/wantesturlimg.jpg',
			'width'		=> 598,
			'height'	=> 425,
			'mime'		=> 'image/png'
		];

		$arrConfig = $this->_getConfig();
		$arrConfig[ 'imgurl' ] = 'http://ofstpx613.bkt.clouddn.com/F85D.tmp.jpg';
		$arrConfig[ 'filename' ] = 'wantesturlimg.jpg';
		$arrRtn = [];
		$nErrCode = \dekuan\deimgsrv\CImgService::addUrlToOSS( $arrConfig, $arrRtn );

		if ( \dekuan\deimgsrv\lib\CErrCode::ERROR_SUCCESS == $nErrCode )
		{
			$bExcept = $this->mySeeJson( $arrRtn, $arrExcept );
			if ( true == $bExcept )
			{
				$this->echoTestResult( true, $sTestName );
			}
			else
			{
				$this->echoTestResult( false, $sTestName, '返回结果验证失败;', $arrExcept, $arrRtn );
			}
		}
		else
		{
			$this->echoTestResult( false, $sTestName, '错误码:' . $nErrCode );
		}
	}

	public function testShowPicture()
	{
		$this->skipTest( TEST_SKIP );
		$sTestName = '显示图片测试';

		$arrExcept = [
		];

		$arrConfig = $this->_getConfig();
		$arrConfig[ 'filename' ] = 'wantesturlimg.jpg';
		$arrRtn = [];
		$nErrCode = \dekuan\deimgsrv\CImgService::ShowPicture( $arrConfig, $arrRtn );

		if ( \dekuan\deimgsrv\lib\CErrCode::ERROR_SUCCESS == $nErrCode )
		{
			$bExcept = $this->mySeeJson( $arrRtn, $arrExcept );
			if ( true == $bExcept )
			{
				$this->echoTestResult( true, $sTestName );
			}
			else
			{
				$this->echoTestResult( false, $sTestName, '返回结果验证失败;', $arrExcept, $arrRtn );
			}
		}
		else
		{
			$this->echoTestResult( false, $sTestName, '错误码:' . $nErrCode );
		}
	}


	public function testShowPictureNoFileName()
	{
		$this->skipTest( TEST_SKIP );
		$sTestName = '显示图片测试';

		$arrConfig = $this->_getConfig();
		$arrRtn = [];
		$nErrCode = \dekuan\deimgsrv\CImgService::ShowPicture( $arrConfig, $arrRtn );

		if ( \dekuan\deimgsrv\lib\CErrCode::ERR_SHOW_IMG_WITH_NAME_PARA_FILE_NAME == $nErrCode )
		{
			$this->echoTestResult( true, $sTestName );
		}
		else
		{
			$this->echoTestResult( false, $sTestName, '错误码:' . $nErrCode, \dekuan\deimgsrv\lib\CErrCode::ERR_SHOW_IMG_WITH_NAME_PARA_FILE_NAME, $nErrCode );
		}
	}

	private function _getConfig()
	{
		return [
			'appid'       => '',
			'security'    => '',
			'fieldname'   => 'dekuanfile',
			'bucket'      => 'deimage',
			'bktinnerurl' => 'oss-cn-beijing-internal.aliyuncs.com',
			'bkturl'      => 'oss-cn-beijing.aliyuncs.com',
			'url'         => 'img.dekuan.org',
			'timeout'     => '5',
			'useinner'    => 0,
			'domain'      => 'http://deimage.dekuan.org'
		];
	}


	private function _getAnotherConfig()
	{
		return [
			'appid'       => '',
			'security'    => '',
			'fieldname'   => 'dekuanfile',
			'bucket'      => 'deimage',
			'bktinnerurl' => 'oss-cn-beijing-internal.aliyuncs.com',
			'bkturl'      => 'oss-cn-beijing.aliyuncs.com',
			'url'         => 'img.dekuan.org',
			'timeout'     => '5',
			'useinner'    => 0,
			'domain'      => 'http://deimage.dekuan.org'
		];
	}
}
