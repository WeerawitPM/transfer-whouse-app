<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Fccode_Glref extends Model
{
    protected $connection = 'formula';
    protected $table = 'GLREF';

    /**
     * Get the next FCCODE for a given year and month.
     * 
     * @param string $bookFcsKid
     * @param int $year
     * @param int $month
     * @return int
     */
    public static function get_fccode_glref($bookFcsKid, $year, $month)
    {
        // Initialize the variables
        $num = 0;
        $num1 = '0';

        // Prepare the query to get the current FCCODE
        $result = DB::connection('formula')->selectOne(
            'DECLARE @NUM INT = 0;
            DECLARE @NUM1 NVARCHAR(100) = \'0\';

            SELECT TOP 1 @NUM = (FCCODE + 1)
            FROM [formula].[dbo].[GLREF] 
            WHERE FCCORP = \'H2ZFEv02\' 
            AND FCBOOK = LTRIM(RTRIM(?))  
            AND YEAR(FDDATE) = ?
            AND MONTH(FDDATE) = ?
            AND LEN(FCCODE) = 9
            ORDER BY RIGHT(LTRIM(RTRIM(REPLACE(REPLACE(REPLACE([FCCODE], CHAR(32), \'()\'), \')(\', \'\'), \'()\', CHAR(32)))), 9) DESC;

            IF @NUM = 0
            BEGIN
                SET @NUM1 = CONCAT(\'67\', ?, \'00000\');
                SET @NUM = CAST(@NUM1 AS INT);
            END

            SELECT @NUM + 1 AS FCCODE',
            [
                $bookFcsKid, // First parameter for FCBOOK
                $year,       // Second parameter for YEAR(FDDATE)
                $month,      // Third parameter for MONTH(FDDATE)
                $month       // Fourth parameter for CONCAT('67', month, '00000')
            ]
        );

        // Return the FCCODE or set it to 0 if no result
        return $result ? (int) $result->FCCODE : 0;
    }
}
