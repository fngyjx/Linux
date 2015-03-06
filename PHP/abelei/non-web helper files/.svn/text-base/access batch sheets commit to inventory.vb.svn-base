Private Sub btnCommitToInventory_Click()
Dim db As Database
Dim rsPendingInventory As Recordset
Dim rsPendingInventoryForPackaging  As Recordset
Dim rsBatchSheetDetail As Recordset
Dim rsBatchSheetDetailCustomerInfo As Recordset

Dim dblCurrentPackagingInventory As Double
Dim strProductNumberInternal As String
Dim dblPercentage As Double
Dim strBatchUnits As String
Dim varInventoryUnits As String
Dim dblconvertedquantity As Double
Dim varLookupFinalProductNotCreatedByAbelei As Double
Dim sql As String
Dim msg, Style, Title, Help, Ctxt, Response, MyString As String
Dim x As String
Dim varInventoryCommitted As Variant
Dim varIsItIntermediary As Variant
Dim varLookForContractPackaging As Variant

Set db = DBEngine.Workspaces(0).Databases(0)

'check to make sure that there is a due date before they can commit to inventory
If IsNull(Me![DueDate]) Then
   msg = "You must enter a due date before you can commit to inventory!!!"
   Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
   Title = " Commit to Inventory ERROR"    ' Define title.
   Response = MsgBox(msg, Style, Title)
   Exit Sub
End If

Forms!Template!CurrentBatchSheetNumber = Me![BatchSheetNumber]



'first check that all the ingredients are in inventory
'If any ingredient does not have an inventory record then the process will be cancelled.
DoCmd.SetWarnings False
If Me![Manufactured] = True Then
   msg = "This product has already been manufactured and cannot be removed from committed inventory!!!"
   Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
   Title = " Commit to Inventory ERROR"    ' Define title.
   Response = MsgBox(msg, Style, Title)
   Exit Sub
End If

varLookForContractPackaging = DLookup("[PackIn]", "BatchSheetCustomerInfo", "[Packin] = '600012' And [BatchSheetNumber] = " & Me![BatchSheetNumber] & "")
varIsItIntermediary = DLookup("[Intermediary]", "ProductMaster", "[ProductNumberInternal] = """ & Me![ProductNumberInternal] & """")
If varIsItIntermediary = 0 Then
   If IsNull(varLookForContractPackaging) Then 'ignore intermediaries and contract packaging

      'check to see that packaging was selected for every PO
      sql = "SELECT BatchSheetCustomerInfo.BatchSheetNumber, BatchSheetCustomerInfo.PackIn, BatchSheetCustomerInfo.NumberOfPackages"
      sql = sql + " FROM BatchSheetCustomerInfo WHERE ((BatchSheetCustomerInfo.BatchSheetNumber)=" & BatchSheetNumber & " )"

      Set rsBatchSheetDetailCustomerInfo = db.OpenRecordset(sql, dbOpenDynaset)
      If rsBatchSheetDetailCustomerInfo.RecordCount > 0 Then
         rsBatchSheetDetailCustomerInfo.MoveFirst
         Do Until rsBatchSheetDetailCustomerInfo.EOF = True
            If IsNull(rsBatchSheetDetailCustomerInfo![PackIn]) Then
               If IsNull(rsBatchSheetDetailCustomerInfo![NumberOfPackages]) Then
                  msg = "Before you can commit to inventory, you must enter the 'pack in' and 'pack in QTY' fields for each P.O."
                  Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
                  Title = " Commit to Inventory 'PACK IN' ERROR"    ' Define title.
                  Response = MsgBox(msg, Style, Title)
                  Exit Sub
               Else
                  msg = "Before you can commit to inventory, you must enter the 'pack in' and 'pack in QTY' fields for each P.O."
                  Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
                  Title = " Commit to Inventory 'PACK IN' ERROR"    ' Define title.
                  Response = MsgBox(msg, Style, Title)
                  Exit Sub
               End If
            Else
               If IsNull(rsBatchSheetDetailCustomerInfo![NumberOfPackages]) Then
                  msg = "Before you can commit to inventory, you must enter the 'pack in' and 'pack in QTY' fields for each P.O."
                  Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
                  Title = " Commit to Inventory 'PACK IN' ERROR"    ' Define title.
                  Response = MsgBox(msg, Style, Title)
                  Exit Sub
               Else
                  rsBatchSheetDetailCustomerInfo.MoveNext
               End If
            End If
      
         Loop
      Else
          msg = "Before you can commit to inventory, you must enter the customer P.O. information"
          Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
          Title = " Commit to Inventory 'PACK IN' ERROR"    ' Define title.
          Response = MsgBox(msg, Style, Title)
          Exit Sub

      End If
      rsBatchSheetDetailCustomerInfo.Close
      
   End If
End If

If Me![btnCommitToInventory].Caption = "Commit to Inventory" Then
   If CheckIngredientsHaveInventory(Me![BatchSheetNumber]) = False Then ' Stop
      msg = "At least one ingredient in this batch sheet does not have an inventory record. All ingredients must have inventory records to complete the 'Commit to Inventory' process. The process will be cancelled."
      Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
      Title = " Commit to Inventory ERROR"    ' Define title.
      Response = MsgBox(msg, Style, Title)
      Exit Sub
   'Else
     'check if there is packaging in inventory for all po's
   'WHY?
    ' sql = "SELECT BatchSheetCustomerInfo.BatchSheetNumber, BatchSheetCustomerInfo.PackIn, BatchSheetCustomerInfo.NumberOfPackages"
    ' sql = sql + " FROM BatchSheetCustomerInfo WHERE ((BatchSheetCustomerInfo.BatchSheetNumber)=" & BatchSheetNumber & " )"

   '  Set rsBatchSheetDetailCustomerInfo = db.OpenRecordset(sql, dbOpenDynaset)
   '  rsBatchSheetDetailCustomerInfo.MoveFirst
   '  Do Until rsBatchSheetDetailCustomerInfo.EOF = True
   '     dblCurrentPackagingInventory = CalCCurrentInventoryAllLots(rsBatchSheetDetailCustomerInfo![PackIn])
    '    varInventoryCommitted = DLookup("[AmountCommitted]", "PendingInventoryChanges", "[ProductNumberInternal]= """ & rsBatchSheetDetailCustomerInfo![PackIn] & """")
   '     dblCurrentPackagingInventory = dblCurrentPackagingInventory - varInventoryCommitted - rsBatchSheetDetailCustomerInfo![NumberOfPackages]
    '    If dblCurrentPackagingInventory <= 0 Then
    '       Msg = "There is not enough packaging material in inventory. This product cannot be committed to inventory at this time."
    '       Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
    '       Title = " Commit to Inventory ERROR"    ' Define title.
    '       Response = MsgBox(Msg, Style, Title)
    '       Exit Sub
    '    End If
    '    rsBatchSheetDetailCustomerInfo.MoveNext
   '  Loop
    ' rsBatchSheetDetailCustomerInfo.Close
  End If
End If

If Me![NetWeight] > 0 And Not IsNull(Me![Total_Unit_Type]) And Me![Yield] > 0 And Me![NumberOfTimesToMake] > 0 Then
'go to the next step
Else
   msg = "The 'Net Weight', 'Total Units', 'Yield' and 'Nbr Of Times To Make' must be filled in before this batch sheet can be committed to inventory!!!"
   Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
   Title = " Commit to Inventory ERROR"    ' Define title.
   Response = MsgBox(msg, Style, Title)
   Exit Sub
End If

  'update the inventory committed field for the packaging
   sql = "SELECT BatchSheetCustomerInfo.BatchSheetNumber, BatchSheetCustomerInfo.PackIn, BatchSheetCustomerInfo.NumberOfPackages"
   sql = sql + " FROM BatchSheetCustomerInfo WHERE ((BatchSheetCustomerInfo.BatchSheetNumber)=" & BatchSheetNumber & " )"

   Set rsBatchSheetDetailCustomerInfo = db.OpenRecordset(sql, dbOpenDynaset)
   rsBatchSheetDetailCustomerInfo.MoveFirst
   Do Until rsBatchSheetDetailCustomerInfo.EOF = True
      If Not IsNull(rsBatchSheetDetailCustomerInfo![PackIn]) And rsBatchSheetDetailCustomerInfo![NumberOfPackages] > 0 Then
         sql = "SELECT PendingInventoryChanges.AmountCommitted, PendingInventoryChanges.ProductNumberInternal"
         sql = sql + " FROM PendingInventoryChanges"
         sql = sql + " WHERE (((PendingInventoryChanges.ProductNumberInternal)=""" & rsBatchSheetDetailCustomerInfo![PackIn] & """));"
         Set rsPendingInventoryForPackaging = db.OpenRecordset(sql, dbOpenDynaset)
         rsPendingInventoryForPackaging.MoveFirst
         rsPendingInventoryForPackaging.Edit
         If Me![btnCommitToInventory].Caption = "Commit to Inventory" Then 'add to committed amount
            rsPendingInventoryForPackaging![AmountCommitted] = rsPendingInventoryForPackaging![AmountCommitted] + rsBatchSheetDetailCustomerInfo![NumberOfPackages]
         Else
            'subtract from the committed amount
            rsPendingInventoryForPackaging![AmountCommitted] = rsPendingInventoryForPackaging![AmountCommitted] - rsBatchSheetDetailCustomerInfo![NumberOfPackages]
          '  Me![PackInventoryAvailable] = ""
         End If
         rsPendingInventoryForPackaging.Update
         rsPendingInventoryForPackaging.Close
      End If
      rsBatchSheetDetailCustomerInfo.MoveNext
   Loop
   rsBatchSheetDetailCustomerInfo.Close
 'update the inventory committed field for each ingredient
   
   sql = "SELECT BatchSheetDetail.BatchSheetNumber, BatchSheetDetail.IngredientProductNumber, BatchSheetDetail.Percentage"
   sql = sql + " FROM BatchSheetDetail WHERE ((BatchSheetDetail.BatchSheetNumber)=" & BatchSheetNumber & " and Not ((BatchSheetDetail.IngredientProductNumber) Like '4*') )"

   Set rsBatchSheetDetail = db.OpenRecordset(sql, dbOpenDynaset)

   rsBatchSheetDetail.MoveFirst
   Do Until rsBatchSheetDetail.EOF = True
      rsBatchSheetDetail.Edit
      strProductNumberInternal = rsBatchSheetDetail![IngredientProductNumber]
      dblPercentage = rsBatchSheetDetail![Percentage]
      
      sql = "SELECT PendingInventoryChanges.ProductNumberInternal, PendingInventoryChanges.AmountCommitted"
      sql = sql + " FROM PendingInventoryChanges WHERE ((PendingInventoryChanges.ProductNumberInternal)= """ & strProductNumberInternal & """)"

      Set rsPendingInventory = db.OpenRecordset(sql, dbOpenDynaset)
      rsPendingInventory.MoveFirst
      rsPendingInventory.Edit
      varInventoryUnits = DLookup("[UnitOfMeasure]", "PendingInventoryChanges", "[ProductNumberInternal]= """ & strProductNumberInternal & """")
      'convert to the unit of measure used in inventory
      dblconvertedquantity = CalculateBatchSheetQuantity(Me![NetWeight], dblPercentage, Me![Yield], Me![TotalQuantityUnitType], varInventoryUnits)
      
      If Me![btnCommitToInventory].Caption = "Commit to Inventory" Then 'add to committed amount
         rsPendingInventory![AmountCommitted] = Round((dblconvertedquantity * Me![NumberOfTimesToMake]) + rsPendingInventory![AmountCommitted], 2)
         Me![NetWeight].Locked = True
         Me![Total_Unit_Type].Locked = True
         Me![Column1_Unit_Type].Locked = True
         Me![Column2_Unit_Type].Locked = True
         Me![Yield].Locked = True
         Me![NumberOfTimesToMake].Locked = True

      Else
       ' subtract from the committed amount
        rsPendingInventory![AmountCommitted] = Round(rsPendingInventory![AmountCommitted] - (dblconvertedquantity * Me![NumberOfTimesToMake]), 2)
        Me![NetWeight].Locked = False
        Me![Total_Unit_Type].Locked = False
        Me![Column1_Unit_Type].Locked = False
        Me![Column2_Unit_Type].Locked = False
        Me![Yield].Locked = False
        Me![NumberOfTimesToMake].Locked = False

      End If
      rsPendingInventory.Update
      rsPendingInventory.Close
      rsBatchSheetDetail.MoveNext
   Loop
   
   rsBatchSheetDetail.Close
   If Me![btnCommitToInventory].Caption = "Commit to Inventory" Then 'add to committed amount
      Me![CommitedToInventory] = True
      Me![btnCommitToInventory].Caption = "	 From Committed Inventory"
      msg = "Batch Sheet ingredients successfully committed to inventory!"
      Style = vbOKOnly + vbInformation + vbDefaultButton2    ' Define buttons.
      Title = " Commit to Inventory "    ' Define title.
      Response = MsgBox(msg, Style, Title)
   Else
      Me![CommitedToInventory] = False
      Me![btnCommitToInventory].Caption = "Commit To Inventory"
      msg = "Batch Sheet ingredients successfully removed from committed inventory!"
      Style = vbOKOnly + vbInformation + vbDefaultButton2    ' Define buttons.
      Title = " Remove From Committed Inventory "    ' Define title.
      Response = MsgBox(msg, Style, Title)
   End If
   DoCmd.DoMenuItem acFormBar, acRecordsMenu, acSaveRecord, , acMenuVer70
   Call Form_Load
   Me.Requery
   DoCmd.SetWarnings True
End Sub

Public Function CheckIngredientsHaveInventory(BatchSheetNumber)
Dim db As Database
Dim sql As String
Dim rsBatchSheetDetail As Recordset
Dim strProductNumberInternal As String
Dim varCheckForInventoryRecord As Variant

Set db = DBEngine.Workspaces(0).Databases(0)
'check to see that inventory records exist for all ingredients in a batch sheet

sql = "SELECT BatchSheetDetail.BatchSheetNumber, BatchSheetDetail.IngredientProductNumber"
sql = sql + " FROM BatchSheetDetail WHERE ((BatchSheetDetail.BatchSheetNumber)=" & BatchSheetNumber & ")"

Set rsBatchSheetDetail = db.OpenRecordset(sql, dbOpenDynaset)

rsBatchSheetDetail.MoveFirst
Do Until rsBatchSheetDetail.EOF = True
   rsBatchSheetDetail.Edit
   If Not rsBatchSheetDetail![IngredientProductNumber] Like "4*" Then
      strProductNumberInternal = rsBatchSheetDetail![IngredientProductNumber]
      varCheckForInventoryRecord = DLookup("[ProductNumberInternal]", "PendingInventoryChanges", "[ProductNumberInternal]= """ & strProductNumberInternal & """")
      If IsNull(varCheckForInventoryRecord) Then
         CheckIngredientsHaveInventory = False
         rsBatchSheetDetail.Close
         Exit Function
      Else
         rsBatchSheetDetail.MoveNext
      End If
   Else
      rsBatchSheetDetail.MoveNext

   End If
Loop
rsBatchSheetDetail.Close
CheckIngredientsHaveInventory = True

End Function

Public Function CalculateBatchSheetQuantity(NetWeight, Percentage, Yield, Total_Unit_Type, Column_Unit_Type) As Single
Dim sglIngredient_Quantity As Single

Select Case Total_Unit_Type
       Case "lbs"
          sglIngredient_Quantity = ((NetWeight * Percentage) * (1 + (1 - Yield)) / 100)
          sglIngredient_Quantity = Round(sglIngredient_Quantity, 2)
          If Column_Unit_Type = "kg" Then
             sglIngredient_Quantity = Round((sglIngredient_Quantity * 0.4535925), 2)
          Else
             If Column_Unit_Type = "grams" Then
                sglIngredient_Quantity = ((NetWeight * Percentage) * (1 + (1 - Yield)) / 100)
                sglIngredient_Quantity = Round((sglIngredient_Quantity * 453.5925), 3)
             End If
          End If
          
       Case "grams"
          sglIngredient_Quantity = ((NetWeight * Percentage) * (1 + (1 - Yield)) / 100)
          sglIngredient_Quantity = Round(sglIngredient_Quantity, 3)

          If Column_Unit_Type = "lbs" Then
             sglIngredient_Quantity = ((NetWeight * Percentage) * (1 + (1 - Yield)) / 100)
             sglIngredient_Quantity = Round((sglIngredient_Quantity / 453.5925), 2)
          Else
            If Column_Unit_Type = "kg" Then
               sglIngredient_Quantity = ((NetWeight * Percentage) * (1 + (1 - Yield)) / 100)
               sglIngredient_Quantity = Round((sglIngredient_Quantity / 1000), 2)
            End If
          End If
        
      Case "kg"
          sglIngredient_Quantity = ((NetWeight * Percentage) * (1 + (1 - Yield)) / 100)
          sglIngredient_Quantity = Round(sglIngredient_Quantity, 2)

          If Column_Unit_Type = "lbs" Then
             sglIngredient_Quantity = ((NetWeight * Percentage) * (1 + (1 - Yield)) / 100)
             sglIngredient_Quantity = Round((sglIngredient_Quantity / 0.4535925), 2)
          Else
            If Column_Unit_Type = "grams" Then
               sglIngredient_Quantity = ((NetWeight * Percentage) * (1 + (1 - Yield)) / 100)
               sglIngredient_Quantity = Round((sglIngredient_Quantity * 1000), 3)
            End If
          End If
          
End Select



    CalculateBatchSheetQuantity = sglIngredient_Quantity

End Function