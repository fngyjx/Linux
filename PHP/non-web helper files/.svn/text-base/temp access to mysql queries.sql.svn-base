If Me![Select_A_Record_Type] = 3 Then ' a series 100 record - Raw Material
   sql = "SELECT ProductMaster.ProductNumberInternal, ProductMaster.Designation, ProductMaster.Natural_OR_Artificial,ProductMaster.Kosher, ExternalProductNumberReference.ProductNumberExternal FROM ExternalProductNumberReference RIGHT JOIN ProductMaster ON ExternalProductNumberReference.ProductNumberInternal = ProductMaster.ProductNumberInternal WHERE ((((ProductMaster.ProductNumberInternal) Like '" & strProductRange & "')or ((ProductMaster.ProductNumberInternal) Like '" & strProductRange2 & "')) And ((ProductMaster.Designation) Like '" & strNewDesignation & "')) ORDER BY ProductMaster.ProductNumberInternal"
 
   If Me![Select_A_Record_Type] = 2 Then ' a series 200 record - formula
      sql = "SELECT ProductMaster.ProductNumberInternal, ProductMaster.Designation, ProductMaster.Natural_OR_Artificial,ProductMaster.Kosher,ExternalProductNumberReference.ProductNumberExternal, if(Mid([ProductNumberExternal],1,2)='US',[ProductNumberExternal],BuildExternalSortKeyField1([ProductNumberExternal])) AS field1, if(Mid([ProductNumberExternal],4,1)='a',0,[ProductNumberExternal]) AS Field2, BuildExternalSortKeyField3([ProductNumberExternal]) AS Field3, BuildExternalSortKeyField4([ProductNumberExternal]) AS Field4 FROM ExternalProductNumberReference RIGHT JOIN ProductMaster ON ExternalProductNumberReference.ProductNumberInternal = ProductMaster.ProductNumberInternal WHERE ((((ProductMaster.ProductNumberInternal) Like '2*'')or ((ProductMaster.ProductNumberInternal) Like '5*')) And ((ProductMaster.Designation) Like '" 
 strNewDesignation 
 "')) ORDER BY if(Mid([ProductNumberExternal],1,2)='US',[ProductNumberExternal],BuildExternalSortKeyField1([ProductNumberExternal])), if(Mid([ProductNumberExternal],4,1)='a',0,[ProductNumberExternal]), BuildExternalSortKeyField3([ProductNumberExternal]), BuildExternalSortKeyField4([ProductNumberExternal]);"
 
      If Me![Select_A_Record_Type] = 1 Then ' a series 400 record - Instructions
         sql = "SELECT ProductMaster.ProductNumberInternal, ProductMaster.Designation, ProductMaster.Natural_OR_Artificial,ProductMaster.Kosher,ExternalProductNumberReference.ProductNumberExternal, IIf(Mid([ProductNumberExternal],1,2)='US',[ProductNumberExternal],BuildExternalSortKeyField1([ProductNumberExternal])) AS field1, IIf(Mid([ProductNumberExternal],4,1)='a',0,[ProductNumberExternal]) AS Field2, BuildExternalSortKeyField3([ProductNumberExternal]) AS Field3, BuildExternalSortKeyField4([ProductNumberExternal]) AS Field4 FROM ExternalProductNumberReference RIGHT JOIN ProductMaster ON ExternalProductNumberReference.ProductNumberInternal = ProductMaster.ProductNumberInternal WHERE ((((ProductMaster.ProductNumberInternal) Like '" 
 strProductRange 
 "')or ((ProductMaster.ProductNumberInternal) Like '" 
 strProductRange2 
 "')) And ((ProductMaster.Designation) Like '" 
 strNewDesignation 
 "')) ORDER BY IIf(Mid([ProductNumberExternal],1,2)='US',[ProductNumberExternal],BuildExternalSortKeyField1([ProductNumberExternal])), IIf(Mid([ProductNumberExternal],4,1)='a',0,[ProductNumberExternal]), BuildExternalSortKeyField3([ProductNumberExternal]), BuildExternalSortKeyField4([ProductNumberExternal]);"

    If Me![Select_A_Record_Type] = 4 Then ' a series 700 record - Process
            sql = "SELECT ProductMaster.ProductNumberInternal, ProductMaster.Designation, ProductMaster.Natural_OR_Artificial,ProductMaster.Kosher,ExternalProductNumberReference.ProductNumberExternal, IIf(Mid([ProductNumberExternal],1,2)='US',[ProductNumberExternal],BuildExternalSortKeyField1([ProductNumberExternal])) AS field1, IIf(Mid([ProductNumberExternal],4,1)='a',0,[ProductNumberExternal]) AS Field2, BuildExternalSortKeyField3([ProductNumberExternal]) AS Field3, BuildExternalSortKeyField4([ProductNumberExternal]) AS Field4 FROM ExternalProductNumberReference RIGHT JOIN ProductMaster ON ExternalProductNumberReference.ProductNumberInternal = ProductMaster.ProductNumberInternal WHERE ((((ProductMaster.ProductNumberInternal) Like '" 
 strProductRange 
 "')) And ((ProductMaster.Designation) Like '" 
 strNewDesignation 
 "')) ORDER BY IIf(Mid([ProductNumberExternal],1,2)='US',[ProductNumberExternal],BuildExternalSortKeyField1([ProductNumberExternal])), IIf(Mid([ProductNumberExternal],4,1)='a',0,[ProductNumberExternal]), BuildExternalSortKeyField3([ProductNumberExternal]), BuildExternalSortKeyField4([ProductNumberExternal]);"
 
    If Me![Select_A_Record_Type] = 5 Then  ' a series 600 record - Packaging
               sql = "SELECT ProductMaster.ProductNumberInternal, ProductMaster.Designation, ProductMaster.Natural_OR_Artificial,ProductMaster.Kosher,ExternalProductNumberReference.ProductNumberExternal, IIf(Mid([ProductNumberExternal],1,2)='US',[ProductNumberExternal],BuildExternalSortKeyField1([ProductNumberExternal])) AS field1, IIf(Mid([ProductNumberExternal],4,1)='a',0,[ProductNumberExternal]) AS Field2, BuildExternalSortKeyField3([ProductNumberExternal]) AS Field3, BuildExternalSortKeyField4([ProductNumberExternal]) AS Field4 FROM ExternalProductNumberReference RIGHT JOIN ProductMaster ON ExternalProductNumberReference.ProductNumberInternal = ProductMaster.ProductNumberInternal WHERE ((((ProductMaster.ProductNumberInternal) Like '" 
 strProductRange 
 "')) And ((ProductMaster.Designation) Like '" 
 strNewDesignation 
 "')) ORDER BY IIf(Mid([ProductNumberExternal],1,2)='US',[ProductNumberExternal],BuildExternalSortKeyField1([ProductNumberExternal])), IIf(Mid([ProductNumberExternal],4,1)='a',0,[ProductNumberExternal]), BuildExternalSortKeyField3([ProductNumberExternal]), BuildExternalSortKeyField4([ProductNumberExternal]);"

 
 Public Function BuildExternalSortKeyField1(ExternalNumber)
Dim intx As Integer

If Mid(ExternalNumber, 1, 2) = "US" Then
   BuildExternalSortKeyField1 = ExternalNumber
   Exit Function
Else
   BuildExternalSortKeyField1 = Mid(ExternalNumber, 1, 3)
End If

End Function

Public Function BuildExternalSortKeyField3(ExternalNumber) As Integer
Dim intx As Integer
If Left(ExternalNumber, 2) = "US" Then
   BuildExternalSortKeyField3 = 0
   Exit Function
End If
intx = 5
Do Until Not IsNumeric(Mid(ExternalNumber, intx, 1))
    If IsNumeric(Mid(ExternalNumber, intx, 1)) Then
  
       BuildExternalSortKeyField3 = BuildExternalSortKeyField3 & Mid(ExternalNumber, intx, 1)
       intx = intx + 1
    End If
Loop
End Function

Public Function BuildExternalSortKeyField4(ExternalNumber)
Dim intx As Integer
If Left(ExternalNumber, 2) = "US" Then
   BuildExternalSortKeyField4 = ""
   Exit Function
End If


intx = 5
Do Until Not IsNumeric(Mid(ExternalNumber, intx, 1))
    If IsNumeric(Mid(ExternalNumber, intx, 1)) Then
       intx = intx + 1
    End If
Loop
BuildExternalSortKeyField4 = Mid(ExternalNumber, intx, 5)

End Function