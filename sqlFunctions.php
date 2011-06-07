<?php
if (!isset($__SQL_FUNCTIONS__PHP))
{
   $__SQL_FUNCTIONS__PHP = "YES";

   //STD user
   $mysqlHost = 'localhost';
   $mysqlUser = '';
   $mysqlPass = '';

   //Remote VM Setting
   //$mysqlHost = 'cslvm97:3306';
   //$mysqlUser = 'root';
   //$mysqlPass = 'ilovedata';

   
   $mysqlDatabase = 'cplop';
   //$mysqlDatabase = 'test_cplop';
   
   /**
    * Get a connection that is set to the cplop database.
    * An error will result in a die().
    *
    * @.post The caller owns the connection and should close it when finished.
    *
    * @return A connection pointed towards cplop.
    */
   function getConnection()
   {
      //$conn = mysqli_connect($GLOBALS['mysqlHost'], $GLOBALS['mysqlUser'], $GLOBALS['mysqlPass']);
      $conn = mysqli_connect($GLOBALS['mysqlHost'], $GLOBALS['mysqlUser'], 
       $GLOBALS['mysqlPass'], $GLOBALS['mysqlDatabase']);
      
      if (!$conn)
      {
         die('Could not connect to database: ' . mysqli_connect_error());
      }

      //mysql_select_db($conn, $GLOBALS['mysqlDatabase']);

      return $conn;
   }

   /**
    * Query the database and return the result.
    * An error will result in a die().
    *
    * @param query The query to execute.
    *
    * @return The result of the query.
    */
   function query($query)
   {
      $conn = getConnection();
     
      //$query = 'SELECT * from host_species;';

      //$res = mysqli_query($query, $conn);
      $res = mysqli_query($conn, $query);

      if (!$res)
      {
         die('Invalid query: ' . mysqli_error($conn));
      }

      mysqli_close($conn);

      return $res;
   }

   /**
    * Run very simplistic filtering on a string.
    * Replace whitespace and carriage return with a single space and use mysql escape.
    *
    * @param input The string to filter.
    *
    * @return A filtered string.
    *
    * @TODO: The null check is because csc machines (vogon) does not come with
    *  the mysql functions. When we are off those machines, remove the check.
    */
   function simpleFilter($input)
   {
      $res = mysql_real_escape_string(preg_replace('/[\r\s]+/', '', $input));

      if (!$res)
      {
         return preg_replace('/[\r\s]+/', '', $input);
      }

      return $res;
   }
}
?>
