<?php

class TestBase extends PHPUnit_Framework_TestCase
{
	public function skipTest( $nSkip )
	{
		if ( 1 === $nSkip )
		{
			$this->markTestSkipped();
		}
	}

	public function echoTestResult( $bIsSucc, $sContent, $sExtend = null, $exceptInfo = null, $result = null )
	{
		$sMessage = "\n" . '[' . date( 'Ymd H:i:s' ) . ']';

		if ( $bIsSucc )
		{
			$sMessage .= '[INFO]';
		}
		else
		{
			$sMessage .= '[ERR]';
		}

		if ( is_string( $sContent ) )
		{
			if ( $bIsSucc )
				$sMessage .= $sContent . '测试通过' ;
			else
				$sMessage .= $sContent . '测试未通过';

			if ( is_string( $sExtend ) && ! $bIsSucc )
			{
				$sMessage .= "; 原因: " . $sExtend;
			}
		}

		$sMessage .= "\n";

		echo $sMessage;

		if ( ! is_null( $exceptInfo ) )
		{
			echo '预期结果:' . "\n";
			print_r( $exceptInfo );
			echo "\n";
		}

		if ( ! is_null( $result ) )
		{
			echo '实际结果:' . "\n";
			print_r( $result );
			echo "\n";
		}

		if ( $bIsSucc )
		{
			$this->assertTrue( true );
		}
		else
		{
			$this->assertTrue( false );
		}
	}


	public function mySeeJson ( $arrResultArr, $arrExceptArr, $sSkipSym = '@SKIP@' )
	{
		if ( ! is_array( $arrResultArr ) )
		{
			return false;
		}

		if ( ! is_array( $arrExceptArr ) )
		{
			return false;
		}

		$bRtn = false;

		if ( 0 != count( $arrExceptArr ) )
		{
			foreach ( $arrExceptArr as $key => $value )
			{
				$bRtn = false;
				if ( array_key_exists( $key, $arrResultArr ) )
				{
					if ( is_array( $value ) )
					{
						if ( is_array( $arrResultArr[ $key ] ) )
						{
							if ( count( $value ) > 0 && count( $arrResultArr[ $key ] ) > 0 )
							{
								$bRtn = $this->mySeeJson( $arrResultArr[ $key ], $value );
							}
							else if ( count( $value ) == 0 && count( $arrResultArr[ $key ] ) == 0 )
							{
								$bRtn = true;
							}
						}
					}
					else
					{
						if ( is_string( $value ) && 0 === strcmp( $value, $sSkipSym ) )    //	只验证key是否存在,而不验证具体值是否相等
						{
							$bRtn = true;
						}
						else if ( $value === $arrResultArr[ $key ] )
						{
							$bRtn = true;
						}
					}
				}

				if ( ! $bRtn )
				{
					break;
				}
			}
		}
		else if ( 0 == count( $arrExceptArr) && 0 == count( $arrResultArr ) )
		{
			$bRtn = true;
		}

		return $bRtn;
	}
}