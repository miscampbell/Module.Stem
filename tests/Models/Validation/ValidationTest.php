<?php
/*
 * Suspended while validation is in flux

namespace Rhubarb\Stem\Tests\Models\Validation;

use Rhubarb\Stem\Tests\Fixtures\User;
use Rhubarb\Crown\Tests\RhubarbTestCase;

class ValidationTest extends RhubarbTestCase
{
	public function testValidationGetsLabel()
	{
		$equalTo = new EqualTo( "Username", "abc" );

		$this->assertEquals( "Username", $equalTo->label );

		$equalTo = new EqualTo( "EarTagNumber", "abc" );

		$this->assertEquals( "Ear Tag Number", $equalTo->label );
	}

	public function testValidationCanBeInverted()
	{
		$equalTo = new EqualTo( "Username", "abc" );
		$notEqualTo = $equalTo->Invert();

		$user = new User();
		$user->Username = "def";

		$this->assertTrue( $notEqualTo->Validate( $user ) );

		$user->Username = "abc";
		$this->setExpectedException( "Gcd\Core\Modelling\Exceptions\ValidationErrorException" );
		$notEqualTo->Validate( $user );
	}
}

*/