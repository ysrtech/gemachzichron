export const TRANSACTION_STATUSES = {
  Success: 1,
  Pending: 2 ,
  Fail: 3 ,
  Resolved: 4,
}
export const TRANSACTION_STATUS_COLORS = {
  1: 'green',
  2: 'yellow',
  3: 'red',
  4: 'gray',
}

export const TRANSACTION_TYPES = {
  'Base Transaction': 'Base Transaction', // original full amount
  'Main Transaction': 'Main Transaction', // base amount after splitting
  'Membership Fee': 'Membership Fee',
  'Processing Fee': 'Processing Fee',
  'Decline Fees': 'Decline Fees',
}
