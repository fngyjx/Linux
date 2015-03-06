SELECT 
	ProductMaster.ProductNumberInternal, 
	ProductMaster.Designation, 
	ProductMaster.Natural_OR_Artificial, 
	ProductMaster.Kosher, 
	ExternalProductNumberReference.ProductNumberExternal, 
	if( 
		Mid( ExternalProductNumberReference.ProductNumberExternal, 1, 2 ) = 'US', 
			ExternalProductNumberReference.ProductNumberExternal, 
			BuildExternalSortKeyField1( ExternalProductNumberReference.ProductNumberExternal)
	) AS field1, 
	if( 
		Mid( ExternalProductNumberReference.ProductNumberExternal, 4, 1 ) = 'a', 
			0, 
			ExternalProductNumberReference.ProductNumberExternal 
	) AS Field2, 
	BuildExternalSortKeyField3( ExternalProductNumberReference.ProductNumberExternal) AS Field3, 
	BuildExternalSortKeyField4( ExternalProductNumberReference.ProductNumberExternal) AS Field4
FROM ExternalProductNumberReference
	RIGHT JOIN ProductMaster 
	ON ExternalProductNumberReference.ProductNumberInternal = ProductMaster.ProductNumberInternal
WHERE (
	( ( ( ProductMaster.ProductNumberInternal ) LIKE '2%' ) OR ( ( ProductMaster.ProductNumberInternal ) LIKE '5%' ) )
	AND ( ( ProductMaster.Designation ) LIKE '%colin%' )
	)
ORDER BY 
	if( 
		Mid( ExternalProductNumberReference.ProductNumberExternal, 1, 2 ) = 'US', 
			ExternalProductNumberReference.ProductNumberExternal, 
			BuildExternalSortKeyField1( ExternalProductNumberReference.ProductNumberExternal ) 
		) , 
	if( 
		Mid( ExternalProductNumberReference.ProductNumberExternal, 4, 1 ) = 'a', 
		0, 
		ExternalProductNumberReference.ProductNumberExternal 
	) , 
	BuildExternalSortKeyField3( ExternalProductNumberReference.ProductNumberExternal), 
	BuildExternalSortKeyField4( ExternalProductNumberReference.ProductNumberExternal)


Create Function BuildExternalSortKeyField1(ExternalNumber VARCHAR(20))
	RETURNS VARCHAR(20)

	BEGIN
	DECLARE r VARCHAR(20);
	
	If Mid(ExternalNumber, 1, 2) = "US" THEN 
		SET r = ExternalNumber;
	Else
	   SET r = Mid(ExternalNumber, 1, 3);
	End If;
	RETURN(r);
	END|

Create Function BuildExternalSortKeyField3(ExternalNumber VARCHAR(20))
	RETURNS INT
	
	BEGIN
	DECLARE r INT;
	DECLARE intx INT;
	
	If Left(ExternalNumber, 2) = "US" Then
	   SET r = 0;
	ELSE
		SET intx = 5;
		WHILE IsNumeric( MID(ExternalNumber,intx,1)) DO
			If IsNumeric( MID(ExternalNumber,intx,1)) Then
			   SET r = CONCAT( r, Mid(ExternalNumber, intx, 1) );
			   SET intx = intx + 1;
			End If;
		END WHILE;
	END IF;
	
	RETURN(r);
	End |
	
Create Function BuildExternalSortKeyField4(ExternalNumber VARCHAR(20))
	RETURNS VARCHAR(20)
	
	BEGIN
	DECLARE intx INT;
	DECLARE r VARCHAR(20);
	
	If Left(ExternalNumber, 2) = "US" Then
	   SET r = "";
	ELSE
		SET intx = 5;
		WHILE IsNumeric( MID(ExternalNumber,intx,1)) DO
			If IsNumeric( MID(ExternalNumber,intx,1)) Then
			   SET intx = intx + 1;
			End If;
		END WHILE;
		SET r = Mid(ExternalNumber, intx, 5);
	END IF;
	
	RETURN(r);
End |

CREATE FUNCTION IsNumeric(myVal VARCHAR(1024))
RETURNS TINYINT(1) DETERMINISTIC
RETURN myVal REGEXP '^(-|\\+)?([0-9]+\\.[0-9]*|[0-9]*\\.[0-9]+|[0-9]+)$'; 