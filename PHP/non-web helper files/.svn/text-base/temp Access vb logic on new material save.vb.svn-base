Private Sub btnAddNewRecord_Click()
Dim f As Form
Dim intLengthOfQuickScan As Integer
Dim msg, Style, Title, Help, Ctxt, Response, MyString As String
Dim varCheckIfExternalNumberAlreadyExists As Variant

If MsgBox("Do you want to add the new record?", vbYesNo, "Create New Record?") = vbNo Then
   Exit Sub
End If

REM If Not IsNull(Me![QuickScan]) Then
   REM intLengthOfQuickScan = Len(Me![QuickScan])
   REM If intLengthOfQuickScan > 20 Then
      REM msg = "The QuickScan field can only contain 20 characters. You cannot add the record until this field is changed!"
      REM Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
      REM Title = "Quick Scan field to long"    ' Define title.
      REM Response = MsgBox(msg, Style, Title)
      REM Exit Sub
   REM End If
REM End If
If Me![Select_A_Record_Type] = "2" Then 'check for duplicate external number
   varCheckIfExternalNumberAlreadyExists = DLookup("ProductNumberInternal", "ExternalProductNumberReference", "ProductNumberExternal = """ & Me![ProductNumberExternal] & """")
   If Not IsNull(varCheckIfExternalNumberAlreadyExists) Then
      msg = "The external abelei number you entered is already in use, change the number and hit 'Add New Record' again."
      Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
      Title = "Duplicate external abelei number"    ' Define title.
      Response = MsgBox(msg, Style, Title)
      Exit Sub
   End If
End If


If Me![Select_A_Record_Type] = "2" Or Me![Select_A_Record_Type] = "3" Or Me![Select_A_Record_Type] = "5" Then
   If IsNull(Me![UnitOfMeasure]) Then
      msg = "You must select the unit of measure to enable the system to track inventory. Select the unit of measure and 'Add New Record' again."
      Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
      Title = "Missing Unit of Measure"    ' Define title.
      Response = MsgBox(msg, Style, Title)
      Exit Sub
   End If
End If

btnAddFunction_Click
Set f = Forms!Template!MainBody.Form

f!ProductNumberInternal = Forms!Template!CurrentPN
f!ProductNumberExternal = Me!ProductNumberExternal
f!Designation = Me!New_Designation
f.ProductNumberInternal_AfterUpdate
f.ProductNumberExternal_AfterUpdate
btnCancel_Click
f!ProductNumberExternal.SetFocus
f!ProductNumberExternal.Requery
DoCmd.SetWarnings True

End Sub

Private Sub btnAddRecordThenClone_Click()
Dim db As Database
Dim f As Form
Dim strcloneProductNumberExternal As String
Dim intLengthOfQuickScan As Integer
Dim msg, Style, Title, Help, Ctxt, Response, MyString As String
Dim varLookForOriginalFromFormulationPriceSheet As Variant
Dim strProductNumberInternalToClone As String
Dim rsNewPriceSheetRecord As Recordset
Dim rsNewPriceSheetDetailRecords As Recordset
Dim rsPriceSheetDetailRecordsToClone As Recordset
Dim rsProductToClone As Recordset
Dim strFormOpenedFrom As String

Dim sql As String
Dim varGetPriceSheetNbr As Variant
Dim varGetNewPriceSheetNbr As Variant
Dim varCheckIfExternalNumberAlreadyExists As Variant

Set db = DBEngine.Workspaces(0).Databases(0)

            
strcloneProductNumberExternal = Me![ProductNumberExternalToClone]
strProductNumberInternalToClone = Me![ProductNumberInternalToClone]
strFormOpenedFrom = Me![FormOpenedFrom]

If MsgBox("Do you want to add the new product and clone it from formula " & strcloneProductNumberExternal & " ?", vbYesNo, "Create New Product?") = vbNo Then
   Exit Sub
End If

If Not IsNull(Me![QuickScan]) Then
   intLengthOfQuickScan = Len(Me![QuickScan])
   If intLengthOfQuickScan > 20 Then
      msg = "The QuickScan field can only contain 20 characters. You cannot add the record until this field is changed!"
      Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
      Title = "Quick Scan field to long"    ' Define title.
      Response = MsgBox(msg, Style, Title)
      Exit Sub
   End If
End If

If Me![Select_A_Record_Type] = "2" Then 'check for duplicate external number
   varCheckIfExternalNumberAlreadyExists = DLookup("ProductNumberInternal", "ExternalProductNumberReference", "ProductNumberExternal = """ & Me![ProductNumberExternal] & """")
   If Not IsNull(varCheckIfExternalNumberAlreadyExists) Then
      msg = "The external abelei number you entered is already in use, change the number and try again."
      Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
      Title = "Duplicate external abelei number"    ' Define title.
      Response = MsgBox(msg, Style, Title)
      Exit Sub
   End If
End If
   
If Me![Select_A_Record_Type] = "2" Or Me![Select_A_Record_Type] = "3" Or Me![Select_A_Record_Type] = "5" Then
   If IsNull(Me![UnitOfMeasure]) Then
      msg = "You must select the unit of measure to enable the system to track inventory. Select the unit of measure and 'Add New Record and Clone From Selected Record' again."
      Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
      Title = "Missing Unit of Measure"    ' Define title.
      Response = MsgBox(msg, Style, Title)
      Exit Sub
   End If
End If



btnAddFunction_Click
CloneProduct

Set f = Forms!Template!MainBody.Form

f!ProductNumberInternal = Forms!Template!CurrentPN
f!ProductNumberExternal = Me!ProductNumberExternal
f!Designation = Me!New_Designation
f.ProductNumberInternal_AfterUpdate
btnCancel_Click
f!ProductNumberExternal.Requery
' find out if a price sheet exists for the product being cloned - if it does, use that, otherwise create a new price sheet from scratch

varGetPriceSheetNbr = DLookup("[PriceSheetNumber]", "PriceSheetMaster", "[ProductNumberInternal]=""" & strProductNumberInternalToClone & """ AND [Original_From_Formulation]=True")
If varGetPriceSheetNbr > 0 Then
   'clone the price sheet

   Set rsNewPriceSheetRecord = db.OpenRecordset("PriceSheetMaster", dbOpenDynaset)
  
   rsNewPriceSheetRecord.AddNew
   rsNewPriceSheetRecord!ProductNumberInternal = f!ProductNumberInternal
   rsNewPriceSheetRecord!ProductDesignation = f!Designation
   rsNewPriceSheetRecord![DatePriced] = Format(Date, "mm/dd/yy")
   rsNewPriceSheetRecord![ShippingCost] = DLookup("[ItemValue]", "tblSystemDefaultsDetail", "[Itemid] = 6")
   rsNewPriceSheetRecord![PackagingCost] = DLookup("[ItemValue]", "tblSystemDefaultsDetail", "[Itemid] = 5")
   rsNewPriceSheetRecord![FOBLocation] = "N. Aurora, IL"
   rsNewPriceSheetRecord![Terms] = DLookup("[ItemDescription]", "tblSystemDefaultsDetail", "[Itemid] = 9")
   rsNewPriceSheetRecord![SpecificGravity] = DLookup("[SpecificGravity]", "ProductMaster", "[ProductNumberInternal] = """ & f!ProductNumberInternal & """")
   rsNewPriceSheetRecord![SpecificGravityUnits] = DLookup("[SpecificGravityUnits]", "ProductMaster", "[ProductNumberInternal] = """ & f!ProductNumberInternal & """")
   rsNewPriceSheetRecord![Lbs_Per_Gallon] = rsNewPriceSheetRecord![SpecificGravity] * 8.33
   rsNewPriceSheetRecord![Original_From_Formulation] = True
   rsNewPriceSheetRecord.Update
   
   sql = "SELECT PriceSheetDetail.* FROM PriceSheetDetail"
   sql = sql + " WHERE (((PriceSheetDetail.PriceSheetNumber)=" & varGetPriceSheetNbr & "));"
   Set rsPriceSheetDetailRecordsToClone = db.OpenRecordset(sql, dbOpenDynaset)
   Set rsNewPriceSheetDetailRecords = db.OpenRecordset("PriceSheetDetail", dbOpenDynaset)

   varGetNewPriceSheetNbr = DLookup("[PriceSheetNumber]", "PriceSheetMaster", "[ProductNumberInternal]=""" & f!ProductNumberInternal & """ AND [Original_From_Formulation]=True")
   If rsPriceSheetDetailRecordsToClone.RecordCount > 0 Then
      rsPriceSheetDetailRecordsToClone.MoveFirst
      Do Until rsPriceSheetDetailRecordsToClone.EOF = True
         rsNewPriceSheetDetailRecords.AddNew
         rsNewPriceSheetDetailRecords!PriceSheetNumber = varGetNewPriceSheetNbr
         rsNewPriceSheetDetailRecords!IngredientProductNumber = rsPriceSheetDetailRecordsToClone!IngredientProductNumber
         rsNewPriceSheetDetailRecords!IngredientSEQ = rsPriceSheetDetailRecordsToClone!IngredientSEQ
         rsNewPriceSheetDetailRecords!IngredientDesignation = rsPriceSheetDetailRecordsToClone!IngredientDesignation
         rsNewPriceSheetDetailRecords!Percentage = rsPriceSheetDetailRecordsToClone!Percentage
         rsNewPriceSheetDetailRecords!Price = rsPriceSheetDetailRecordsToClone!Price
         rsNewPriceSheetDetailRecords!PriceEffectiveDate = rsPriceSheetDetailRecordsToClone!PriceEffectiveDate
         rsNewPriceSheetDetailRecords!VendorID = rsPriceSheetDetailRecordsToClone!VendorID
         rsNewPriceSheetDetailRecords!Intermediary = rsPriceSheetDetailRecordsToClone!Intermediary
         rsNewPriceSheetDetailRecords!Tier = rsPriceSheetDetailRecordsToClone!Tier
         rsNewPriceSheetDetailRecords.Update
         rsPriceSheetDetailRecordsToClone.MoveNext
      Loop
   End If
Else
   Call UpdatePriceSheetValues(Forms!Template!MainBody.Form.ProductNumberInternal, Forms!Template!MainBody.Form.Designation, Forms!Template!MainBody.Form.SpecificGravity, Forms!Template!MainBody.Form.SpecificGravityUnits, True)
End If
DoCmd.SetWarnings False
DoCmd.OpenQuery "qry_Delete_TempFormulationAndVendorTable"
DoCmd.OpenQuery "qry_Build_TempFormulationAndVendorTable"
DoCmd.OpenQuery "qry_Update_TempFormulationAndVendorTable"

If strFormOpenedFrom = "Formulations" Then
   Forms![Template].[MainBody]![subFormulation].Form.Requery
End If

f!ProductNumberExternal.Requery

'
f!ProductNumberExternal.SetFocus


DoCmd.SetWarnings True
End Sub