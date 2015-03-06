#!/usr/bin/env sh
bc check_book.bc
# bc 1.06
# Copyright 1991-1994, 1997, 1998, 2000 Free Software Foundation, Inc.
# This is free software with ABSOLUTELY NO WARRANTY.
# For details type `warranty'.
#
# Check book program!
#   Remember, deposits are negative transactions.
#     Exit by a 0 transaction.
#
#     Initial balance? 100
#
#     current balance = 100.00
#     transaction? -200
#     current balance = 300.00
#     transaction? 50
#     current balance = 250.00
#     transaction? -20000000
#     current balance = 20000250.00
#     transaction? 0

#scale=2
#print "\nCheck book program!\n"
#print "  Remember, deposits are negative transactions.\n"
#print "  Exit by a 0 transaction.\n\n"
#
#print "Initial balance? "; bal = read()
#bal /= 1
#print "\n"
#while (1) {
#  "current balance = "; bal
#  "transaction? "; trans = read()
#  if (trans == 0) break;
#    bal -= trans
#    bal /= 1
#}
#quit


