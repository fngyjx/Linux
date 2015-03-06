Private Sub btnManufactured_Click()
Dim strBatchSheetNumber As String
Dim msg, Style, Title, Help, Ctxt, Response, MyString As String
Dim varCheckIfAnYIngredientNotAvailable As Variant
Dim sql As String

strBatchSheetNumber = Me![BatchSheetNumber]

If Me![Manufactured] = True Then
   msg = "This product has already been manufactured and cannot be changed!!!"
   Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
   Title = " Assign Lot Numbers ERROR"    ' Define title.
   Response = MsgBox(msg, Style, Title)
   Exit Sub
End If
' check to see that they have entered a date of manufacture
If IsNull(Me![DateManufactured]) Then
   msg = "You must enter a Date Manufactured to continue!!!"
   Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
   Title = " Date Manufactured Missing"    ' Define title.
   Response = MsgBox(msg, Style, Title)
   Exit Sub
End If

'check to see if any ingredient does not have enough inventory

varCheckIfAnYIngredientNotAvailable = DLookup("[IngredientProductNumber]", "TempBatchSheetCommittedInventory", "[BatchSheetNumber] = " & strBatchSheetNumber & " and [IsInventoryAvailable] = False and Not [IngredientProductNumber] Like '4*'")

If Not IsNull(varCheckIfAnYIngredientNotAvailable) Then
   msg = "At least one ingredient does not have inventory available. Lot numbers cannot be assigned until all ingredients have inventory available!!"
   Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
   Title = " Assign Lot Numbers ERROR"    ' Define title.
   Response = MsgBox(msg, Style, Title)
   Exit Sub
End If
If Me![CommitedToInventory] = True Then
   strBatchSheetNumber = Me![BatchSheetNumber]
   'save the current record
   DoCmd.DoMenuItem acFormBar, acRecordsMenu, acSaveRecord, , acMenuVer70
   DoCmd.OpenForm "frmAssignLotNumbers"
   Forms![frmAssignLotNumbers].[BatchSheetNumber] = strBatchSheetNumber
   Forms![frmAssignLotNumbers]![subBatchSheetAssignLotNumbers].Requery
 '  Forms![frmAssignLotNumbers].[PackagingProductNumber] = Me![PackIn]
'   Forms![frmAssignLotNumbers].[PackagingQty] = Me![NumberOfPackages]

Else
   msg = "You cannot assign lot numbers until the batch sheet is committed to inventory!"
   Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
   Title = " Assign Lot Numbers"    ' Define title.
   Response = MsgBox(msg, Style, Title)

End If

End Sub