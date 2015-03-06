Raw Material
SELECT 
	ProductMaster.ProductNumberInternal, 
	ProductMaster.Designation, 
	ProductMaster.Natural_OR_Artificial, 
	ProductMaster.Kosher, 
	ExternalProductNumberReference.ProductNumberExternal 
	FROM 
	ExternalProductNumberReference 
	RIGHT JOIN 
	ProductMaster 
	ON 
	ExternalProductNumberReference.ProductNumberInternal = ProductMaster.ProductNumberInternal 
	WHERE 
	( 
		( 
			( (ProductMaster.ProductNumberInternal) Like '1%') 
			or ((ProductMaster.ProductNumberInternal) Like '5%')
		) 
		And ((ProductMaster.Designation) Like '%$designation%)
	)
	ORDER BY ProductMaster.ProductNumberInternal
	