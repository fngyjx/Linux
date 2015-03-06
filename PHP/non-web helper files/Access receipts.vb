Private Sub btnApproveReceipt_Click()
Dim db As Database
Dim msg, Style, Title, Help, Ctxt, Response, MyString As String
Dim varCheckIfInventoryRecExists As Variant
Dim varCheckIfPendingInventoryRecExists As Variant
Dim varCheckIfInventoryMovementRecExists As Variant
Dim varInventoryUnits As Variant
Dim strProductNumberInternal As String
Dim strLotNumber As String
Dim dblTotalQuantity As Double
Dim dblAmtReceived As Double
Dim strRemarks As String
Dim rsReceipts As Recordset
Dim sql As String
Dim varQtyExpected As Variant
Dim dblNewQtyExpected As Double
Dim dblInventoryOnOrderAdjustment As Double
Dim intLotSequenceNumber As Integer
btnSave_Click

'Make sure the user has entered all appropriate fields before accepting this receipt


'make sure the the user has entered the approval date and approved by
If IsNull(Me![QualityControlDate]) Or IsNull(Me![QualityControlEmployeeID]) Then
   msg = "To accept this receipt, you must enter a 'QC date' and the 'QC performed by'! One or both of these are missing. Enter them, then try again."
   Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
   Title = " Missing Data"    ' Define title.
   Response = MsgBox(msg, Style, Title)
   DoCmd.CancelEvent
   Exit Sub
End If

If IsNull(Me![ProductNumberInternal]) Or IsNull(Me![LotNumber]) Or IsNull(Me![LotSequenceNumber]) Then
   msg = "You must enter a lot number, lot sequence # and a product number to continue!"
   Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
   Title = " Missing Data"    ' Define title.
   Response = MsgBox(msg, Style, Title)
   DoCmd.CancelEvent
   Exit Sub
End If

If IsNull(Me![Quantity]) Or IsNull(Me![PackSize]) Or IsNull(Me![UnitOfMeasure]) Then
   msg = "Some fields are not input!! The following fields are required to continue: 'quantity', 'unit of measure', 'pack size'"
   Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
   Title = " Missing Data"    ' Define title.
   Response = MsgBox(msg, Style, Title)
   DoCmd.CancelEvent
   Exit Sub
End If
strProductNumberInternal = Me![ProductNumberInternal]
strLotNumber = Me![LotNumber]
intLotSequenceNumber = Me![LotSequenceNumber]

dblTotalQuantity = Me![Quantity] * Me![PackSize]
'check to see if an inventory record is added for this product

varCheckIfPendingInventoryRecExists = DLookup("[ProductNumberInternal]", "PendingInventoryChanges", "[ProductNumberInternal] = """ & strProductNumberInternal & """")
If IsNull(varCheckIfPendingInventoryRecExists) Then
   msg = "The product entered is not currently in inventory. You need to add it to inventory to continue"
   Style = vbYesNo + vbCritical + vbDefaultButton2    ' Define buttons.
   Title = " Inventory Record Does Not Exist"    ' Define title.
   Response = MsgBox(msg, Style, Title)
   DoCmd.CancelEvent
   SendKeys "{esc}"
   Exit Sub

End If

'make sure the user wants to approve this receipt

 msg = "Do you want to accept this receipt? Once accepted, it will be added to inventory."
 Style = vbYesNo + vbInformation + vbDefaultButton2    ' Define buttons.
 Title = " Raw Material Receipt"    ' Define title.
 Response = MsgBox(msg, Style, Title)

If Response = vbNo Then
   Exit Sub
Else
    DoCmd.DoMenuItem acFormBar, acRecordsMenu, acSaveRecord, , acMenuVer70

End If


' If the amt received is greater than the amt expected, change the amt to be subtracted from 'amt on order' to the original amt expected
' otherwise subtract the actual amt received

'If dblTotalQuantity > (Me![QuantityExpected]) Then
 '  dblTotalQuantity = (Me![QuantityExpected])
'End If

'add the lot number tracking record


varQtyExpected = DLookup("[TotalQuantityExpected]", "PurchaseOrderDetail", "[PurchaseOrderNumber] = " & Me![PurchaseOrderNumber] & " And [ProductNumberInternal] = """ & Me![ProductNumberInternal] & """ And [PurchaseOrderSeqNumber] = " & Me![PurchaseOrderSeqNumber] & "")

Me![QuantityExpected] = varQtyExpected

dblAmtReceived = 0
'check to see if any other receipts were received
sql = "SELECT LotNumberTracking.PurchaseOrderNumber, LotNumberTracking.PurchaseOrderSeqNumber, LotNumberTracking.ProductNumberInternal, Sum([Quantity]*[PackSize]) AS AmtReceived"
sql = sql + " FROM LotNumberTracking"
sql = sql + " GROUP BY LotNumberTracking.PurchaseOrderNumber, LotNumberTracking.PurchaseOrderSeqNumber, LotNumberTracking.ProductNumberInternal"
sql = sql + " HAVING (((LotNumberTracking.PurchaseOrderNumber)=" & Me![PurchaseOrderNumber] & ")And ((LotNumberTracking.PurchaseOrderSeqNumber)=" & Me![PurchaseOrderSeqNumber] & ") AND  ((LotNumberTracking.ProductNumberInternal)=""" & Me![ProductNumberInternal] & """));"

Set db = DBEngine.Workspaces(0).Databases(0)

Set rsReceipts = db.OpenRecordset(sql, dbOpenDynaset)
If rsReceipts.RecordCount > 0 Then
   rsReceipts.MoveFirst
   dblAmtReceived = rsReceipts![AmtReceived]
   rsReceipts.Close
End If

If Not dblAmtReceived = dblTotalQuantity Then
   dblTotalQuantity = dblTotalQuantity + dblAmtReceived
End If

If Not dblTotalQuantity = Me![QuantityExpected] Then
   If dblTotalQuantity > Me![QuantityExpected] Then
      msg = "The TOTAL quantity received is greater than the TOTAL quantity expected!! Do you want to continue? If yes, the total quantitiy expected will be updated to reflect the new amount received."
      Style = vbYesNo + vbCritical + vbDefaultButton2    ' Define buttons.
      Title = " Qty Received > Qty Expected"    ' Define title.
      Response = MsgBox(msg, Style, Title)
      If Response = vbNo Then
         DoCmd.CancelEvent
         Exit Sub
      Else
         dblInventoryOnOrderAdjustment = Me![QuantityExpected] - dblAmtReceived
         Call UpdateInventoryAmountOnOrder(Me!ProductNumberInternal, dblInventoryOnOrderAdjustment, Me![UnitOfMeasure], "Subtract")
         Me![SelectPurchaseOrderNumber].Requery
         DoCmd.SetWarnings False
         sql = "UPDATE PurchaseOrderDetail SET PurchaseOrderDetail.TotalQuantityExpected = " & dblTotalQuantity & ""
         sql = sql + " WHERE (((PurchaseOrderDetail.PurchaseOrderNumber)=" & Me![PurchaseOrderNumber] & ") AND ((PurchaseOrderDetail.ProductNumberInternal)=""" & strProductNumberInternal & """) AND ((PurchaseOrderDetail.PurchaseOrderSeqNumber)=" & Me![PurchaseOrderSeqNumber] & "));"
         DoCmd.RunSQL (sql)
         DoCmd.SetWarnings True
      End If
  
 
    Else
      If dblTotalQuantity < Me![QuantityExpected] Then
         msg = "The TOTAL quantity received is less than the TOTAL quantity expected!! Is there another shipment or are there multiple lots?"
         Style = vbYesNo + vbCritical + vbDefaultButton2    ' Define buttons.
         Title = " Qty Received < Qty Expected"    ' Define title.
         Response = MsgBox(msg, Style, Title)
         DoCmd.SetWarnings False
         If Response = vbNo Then
            'reduce the amt on order by the amt received to date
            dblInventoryOnOrderAdjustment = Me![QuantityExpected] - dblAmtReceived
            Call UpdateInventoryAmountOnOrder(Me!ProductNumberInternal, dblInventoryOnOrderAdjustment, Me![UnitOfMeasure], "Subtract")
            Me![SelectPurchaseOrderNumber].Requery
           'update the amt expected to be the new total amt received
            dblNewQtyExpected = Me![QuantityExpected] - (Me![Quantity] * Me![PackSize])
            sql = "UPDATE PurchaseOrderDetail SET PurchaseOrderDetail.TotalQuantityExpected = " & dblNewQtyExpected & ""
            sql = sql + " WHERE (((PurchaseOrderDetail.PurchaseOrderNumber)=" & Me![PurchaseOrderNumber] & ") AND ((PurchaseOrderDetail.ProductNumberInternal)=""" & strProductNumberInternal & """) AND ((PurchaseOrderDetail.PurchaseOrderSeqNumber)=" & Me![PurchaseOrderSeqNumber] & "));"
            DoCmd.RunSQL (sql)
            DoCmd.SetWarnings True
        Else
            Call UpdateInventoryAmountOnOrder(Me!ProductNumberInternal, Me![Quantity] * Me![PackSize], Me![UnitOfMeasure], "Subtract")
            Me![SelectPurchaseOrderNumber].Requery

        End If
      End If
   End If
   
Else
   Call UpdateInventoryAmountOnOrder(Me!ProductNumberInternal, dblTotalQuantity, Me![UnitOfMeasure], "Subtract")

End If
strRemarks = Me![subfrmRawMaterialReceipts_PO]![VendorName] & "- Invoice # " & [VendorInvoiceNumber]

'strRemarks = " Invoice # " & [VendorInvoiceNumber]
'get the inventory units of measure
varInventoryUnits = DLookup("[UnitOfMeasure]", "PendingInventoryChanges", "[ProductNumberInternal]= """ & strProductNumberInternal & """")

varCheckIfInventoryRecExists = DLookup("[BeginningInventory]", "Inventory", "[ProductNumberInternal] = """ & strProductNumberInternal & """ And [LotNumber] = """ & strLotNumber & """ and [LotSequenceNumber] = " & intLotSequenceNumber & "")
If IsNull(varCheckIfInventoryRecExists) Then
   Call CreateInventoryRecords(Me!ProductNumberInternal, Me!LotNumber, Me![LotSequenceNumber])
End If
'create a new movement record
DoCmd.SetWarnings False
DoCmd.OpenQuery "qryAppendApprovedRawMaterialReceipt"
 DoCmd.SetWarnings True

If Me![PurchaseOrderType] = "Process" Then ' if the PO is a 'process' type, then the inventory movement is 'receipt from co-packer', otherwise it is 'receipt from vendor'
   Call CreateRawMaterialReceiptInventoryMovement(Me!ProductNumberInternal, Me!LotNumber, Me![LotSequenceNumber], Me!PackSize, Me!DateReceived, 6, strRemarks, varInventoryUnits, Me![UnitOfMeasure], Me![Quantity], "Pending Raw Material Receipt")
Else
   Call CreateRawMaterialReceiptInventoryMovement(Me!ProductNumberInternal, Me!LotNumber, Me![LotSequenceNumber], Me!PackSize, Me!DateReceived, 1, strRemarks, varInventoryUnits, Me![UnitOfMeasure], Me![Quantity], "Pending Raw Material Receipt")
End If
'update the product master record to indicate that this is the most recent vendor for this ingredient

 sql = "UPDATE ProductMaster SET ProductMaster.MostRecentVendorID = " & Me![VendorID] & ""
 sql = sql + " WHERE (((ProductMaster.ProductNumberInternal)=""" & strProductNumberInternal & """));"
 DoCmd.SetWarnings False
 DoCmd.RunSQL (sql)
 DoCmd.SetWarnings True
 
 btnSave_Click
DoCmd.SetWarnings False

DoCmd.OpenQuery "qryUpdateApprovedRawMaterialReceiptInventoryTranID"
 DoCmd.SetWarnings True

'calculate all receipts to date then,'change the status on the purchase order to "accepted" if all items were received
 dblAmtReceived = 0
'check to see if any other receipts were received
sql = "SELECT LotNumberTracking.PurchaseOrderNumber, LotNumberTracking.PurchaseOrderSeqNumber, LotNumberTracking.ProductNumberInternal, Sum([Quantity]*[PackSize]) AS AmtReceived"
sql = sql + " FROM LotNumberTracking"
sql = sql + " GROUP BY LotNumberTracking.PurchaseOrderNumber, LotNumberTracking.PurchaseOrderSeqNumber, LotNumberTracking.ProductNumberInternal"
sql = sql + " HAVING (((LotNumberTracking.PurchaseOrderNumber)=" & Me![PurchaseOrderNumber] & ")And ((LotNumberTracking.PurchaseOrderSeqNumber)=" & Me![PurchaseOrderSeqNumber] & ") AND  ((LotNumberTracking.ProductNumberInternal)=""" & Me![ProductNumberInternal] & """));"


Set rsReceipts = db.OpenRecordset(sql, dbOpenDynaset)
If rsReceipts.RecordCount > 0 Then
   rsReceipts.MoveFirst
   dblAmtReceived = rsReceipts![AmtReceived]
   rsReceipts.Close
End If


If Not IsNull(Me![PurchaseOrderNumber]) Then
   If dblAmtReceived = Me![QuantityExpected] Then
      sql = "UPDATE PurchaseOrderDetail SET PurchaseOrderDetail.Status = 'A'"
      sql = sql + " WHERE (((PurchaseOrderDetail.PurchaseOrderNumber)=" & Me![PurchaseOrderNumber] & ") AND ((PurchaseOrderDetail.ProductNumberInternal)=""" & Me![ProductNumberInternal] & """) AND ((PurchaseOrderDetail.PurchaseOrderSeqNumber)=" & Me![PurchaseOrderSeqNumber] & "));"
      DoCmd.SetWarnings False
      DoCmd.RunSQL (sql)
      DoCmd.SetWarnings True
   End If
End If
DoCmd.SetWarnings False

DoCmd.OpenQuery "qryDeletePendingRawMaterialReceipt"
DoCmd.SetWarnings True
Forms![Template]![MainBody].Requery
Forms![Template]![MainBody]![btnSelectLotNumber].Requery
Me![SelectPurchaseOrderNumber].Requery

'Me.Requery
btnNew_Click
Me![newRecord] = "True"

End Sub

'----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
'----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
'----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

Private Sub btnRejectReceipt_Click()

Dim sql As String
Dim msg, Style, Title, Help, Ctxt, Response, MyString As String
Dim dblTotalQuantity As Double


'DoCmd.DoMenuItem acFormBar, acRecordsMenu, acSaveRecord, , acMenuVer70
If Not IsNull(Me!LotNumber) Then
   btnSave_Click
End If

If IsNull(Me![QualityControlDate]) Or IsNull(Me![QualityControlEmployeeID]) Then
   msg = "To reject this receipt, you must enter a 'QC date' and the 'QC performed by'! One or both of these are missing. Enter them, then try again."
   Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
   Title = " Missing Data"    ' Define title.
   Response = MsgBox(msg, Style, Title)
   DoCmd.CancelEvent
   Exit Sub
End If

msg = "Do you want to reject this receipt?"
Style = vbYesNo + vbInformation + vbDefaultButton2    ' Define buttons.
Title = " Inbound Receipt Rejection"    ' Define title.
Response = MsgBox(msg, Style, Title)
If Response = vbNo Then
   Me![QualityControlRejectedDate] = Null
   Me![QualityControlEmployeeID] = Null
   Me![QCApprovedBy] = Null
   Exit Sub
End If

dblTotalQuantity = Me![Quantity] * Me![PackSize]

'save the current record in case the user has made some changes

DoCmd.DoMenuItem acFormBar, acRecordsMenu, acSaveRecord, , acMenuVer70


sql = "INSERT INTO RejectedInBoundReceipts SELECT PendingInBoundReceipts.* FROM PendingInBoundReceipts"
sql = sql + " WHERE (((PendingInBoundReceipts.LotNumber)=""" & Me![LotNumber] & """) and ((PendingInBoundReceipts.LotSequenceNumber) = " & Me![LotSequenceNumber] & "));"
DoCmd.SetWarnings False
DoCmd.RunSQL (sql)
DoCmd.SetWarnings True

'change the status on the purchase order to "Rejected"

If Not IsNull(Me![PurchaseOrderNumber]) Then
   sql = "UPDATE PurchaseOrderDetail SET PurchaseOrderDetail.Status = 'R'"
   sql = sql + " WHERE (((PurchaseOrderDetail.PurchaseOrderNumber)=" & Me![PurchaseOrderNumber] & ") AND ((PurchaseOrderDetail.ProductNumberInternal)=""" & Me![ProductNumberInternal] & """) AND ((PurchaseOrderDetail.PurchaseOrderSeqNumber)=" & Me![PurchaseOrderSeqNumber] & "));"
   DoCmd.SetWarnings False
   DoCmd.RunSQL (sql)
  DoCmd.SetWarnings True

End If



'take the amt on order away because the receipt was rejected
   Call UpdateInventoryAmountOnOrder(Me!ProductNumberInternal, dblTotalQuantity, Me![UnitOfMeasure], "Subtract")

'now delete the pending record from the pending table
 DoCmd.SetWarnings False

    DoCmd.DoMenuItem acFormBar, acEditMenu, 8, , acMenuVer70
    DoCmd.DoMenuItem acFormBar, acEditMenu, 6, , acMenuVer70
    
     
    Forms![Template]![MainBody]![btnSelectLotNumber].Requery
    Me![SelectPurchaseOrderNumber].Requery

   Me.Requery
    
   Me![Description] = Null
   Me![EmployeeName] = Null
   Me![SelectPurchaseOrderNumber] = Null
   Me![VendorName] = Null
   DoCmd.SetWarnings True

btnNew_Click
End Sub

'----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
'----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
'----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

Private Sub btnDelete_Click()
Dim strProductNumberInternal As String
Dim strLotNumber As String
Dim dblTotalQuantity As Double
Dim msg, Style, Title, Help, Ctxt, Response, MyString As String
Dim strUnitOfMeasure As String
Dim sql As String
Dim intLotSequenceNumber As Integer

'make sure the user wants to delete this record
If IsNull(Me![LotNumber]) Then
   msg = "There is no lot number so the record cannot be deleted!"
   Style = vbOKOnly + vbCritical + vbDefaultButton2    ' Define buttons.
   Title = " Missing Data"    ' Define title.
   Response = MsgBox(msg, Style, Title)
   DoCmd.CancelEvent
   Exit Sub
End If


msg = "Do you want to delete this record? "
Style = vbYesNo + vbInformation + vbDefaultButton2    ' Define buttons.
Title = " Pending Raw Material Receipt Record Delete"    ' Define title.
Response = MsgBox(msg, Style, Title)

If Response = vbNo Then
   DoCmd.CancelEvent
   Exit Sub
End If


strProductNumberInternal = Me![ProductNumberInternal]
strLotNumber = Me![LotNumber]
intLotSequenceNumber = Me![LotSequenceNumber]

dblTotalQuantity = Me![Quantity] * Me![PackSize]
strUnitOfMeasure = Me![UnitOfMeasure]
If IsNull(Me![Quantity]) Or IsNull(Me![PackSize]) Or IsNull(Me![UnitOfMeasure]) Then
   msg = "Some fields are not input!! The following fields are required to continue: 'quantity', 'unit of measure', 'pack size'"
   Style = vbYesNo + vbCritical + vbDefaultButton2    ' Define buttons.
   Title = " Missing Data"    ' Define title.
   Response = MsgBox(msg, Style, Title)
   DoCmd.CancelEvent
   SendKeys "{esc}"
   Exit Sub
End If

'set the purchase order status back to 'open'
If Not IsNull(Me![PurchaseOrderNumber]) Then
    sql = "UPDATE PurchaseOrderDetail SET PurchaseOrderDetail.Status = 'O'"
    sql = sql + " WHERE (((PurchaseOrderDetail.PurchaseOrderNumber)=" & Me![PurchaseOrderNumber] & ") AND ((PurchaseOrderDetail.ProductNumberInternal)=""" & Me![ProductNumberInternal] & """) AND ((PurchaseOrderDetail.PurchaseOrderSeqNumber)=" & Me![PurchaseOrderSeqNumber] & "));"
    DoCmd.SetWarnings False
    DoCmd.RunSQL (sql)
    DoCmd.SetWarnings True
 End If

'get the inventory units of measure

DoCmd.SetWarnings False
DoCmd.RunSQL (sql)
'delete the current record
    DoCmd.DoMenuItem acFormBar, acEditMenu, 8, , acMenuVer70
    DoCmd.DoMenuItem acFormBar, acEditMenu, 6, , acMenuVer70

Forms![Template]![MainBody]![btnSelectLotNumber].Requery
Me![SelectPurchaseOrderNumber].Requery

Me.Requery
    
   Me![Description] = Null
   Me![EmployeeName] = Null
   Me![SelectPurchaseOrderNumber] = Null
   Me![VendorName] = Null
   

End Sub

'----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
'----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
'----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

Public Function CreateRawMaterialReceiptInventoryMovement(ProductNumberInternal, LotNumber, LotSequenceNumber, Quantity, TransactionDate, TransactionType, Remarks, InventoryUnits, RawMaterialUnits, NumberOfUnits, calledFrom)
Dim db As Database
Dim sql As String
Dim rsInventoryMovements As Recordset
Dim strProductNumberInternal As String
Dim strLotNumber As String
Dim dblconvertedquantity As Double
Dim dblInventoryMovementTransactionID As Double
Dim msg, Style, Title, Help, Ctxt, Response, MyString As String

Set db = DBEngine.Workspaces(0).Databases(0)
Set rsInventoryMovements = db.OpenRecordset("InventoryMovements", dbOpenDynaset)

rsInventoryMovements.AddNew
rsInventoryMovements![ProductNumberInternal] = ProductNumberInternal
rsInventoryMovements![LotNumber] = LotNumber
rsInventoryMovements![LotSequenceNumber] = LotSequenceNumber
rsInventoryMovements![TransactionDate] = TransactionDate
rsInventoryMovements![TransactionType] = TransactionType
dblconvertedquantity = Round((ConvertQuantity(Quantity, RawMaterialUnits, InventoryUnits) * NumberOfUnits), 2)
rsInventoryMovements![Quantity] = dblconvertedquantity
rsInventoryMovements![Remarks] = Remarks
dblInventoryMovementTransactionID = rsInventoryMovements![TransactionNumber]
rsInventoryMovements.Update

'update the lot number tracking record with the Inventory Movement transaction ID

If calledFrom = "Raw Material Receipt" Then
  [Forms]![Template]![MainBody]![subfrmRawMaterialReceipts].Form.[InventoryMovementTransactionNumber] = dblInventoryMovementTransactionID
Else
  If calledFrom = "Batch Sheet" Then
    [Forms]![Template]![MainBody]![InventoryMovementTransactionNumber] = dblInventoryMovementTransactionID

  Else
  If calledFrom = "Pending Raw Material Receipt" Then
     [Forms]![Template]![MainBody]![subfrmPendingRawMaterialReceipts].Form.[InventoryMovementTransactionNumber] = dblInventoryMovementTransactionID
  Else
     msg = "Error in function Create Raw Material Receipt Inventory Movement"
     Style = vbYesNo + vbCritical + vbDefaultButton2    ' Define buttons.
     Title = " Raw Material Receipt Error"    ' Define title.
     Response = MsgBox(msg, Style, Title)

     End If
  End If
End If
rsInventoryMovements.Close


End Function