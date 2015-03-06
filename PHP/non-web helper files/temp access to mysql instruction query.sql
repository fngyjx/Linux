SELECT ProductMaster.ProductNumberInternal, ProductMaster.Designation, ProductMaster.Natural_OR_Artificial,ProductMaster.Kosher,ExternalProductNumberReference.ProductNumberExternal, 
If ( Mid ( [ProductNumberExternal], 1, 2 ) = 'US', [ProductNumberExternal], BuildExternalSortKeyField1 ( [ProductNumberExternal] ) ) AS field1, 
If ( Mid ( [ProductNumberExternal], 4, 1 ) = 'a', 0, [ProductNumberExternal] ) AS Field2, 
BuildExternalSortKeyField3 ( [ProductNumberExternal] ) AS Field3, 
BuildExternalSortKeyField4 ( [ProductNumberExternal] ) AS Field4, 
ProductMaster.Organic 
FROM ExternalProductNumberReference RIGHT JOIN ProductMaster ON ExternalProductNumberReference.ProductNumberInternal = ProductMaster.ProductNumberInternal 
WHERE ( ( ( ( ProductMaster.ProductNumberInternal ) Like '%$productrange%' ) OR ( ( ProductMaster.ProductNumberInternal ) Like '%$productrange2%' ) ) And ( ( ProductMaster.Designation ) Like '%$designation%' ) ) 
ORDER BY If ( Mid ( [ProductNumberExternal], 1, 2 ) = 'US', [ProductNumberExternal], BuildExternalSortKeyField1 ( [ProductNumberExternal] ) ), 
If ( Mid ( [ProductNumberExternal], 4, 1 ) = 'a', 0, [ProductNumberExternal] ), 
BuildExternalSortKeyField3 ( [ProductNumberExternal] ), 
BuildExternalSortKeyField4 ( [ProductNumberExternal] )