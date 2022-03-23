import {AVAILABLE_GATEWAYS} from "@/config/gateways";
import {MEMBERSHIP_TYPES} from "@/config/memberships";

export const SUBSCRIPTION_TYPES = {
  'Membership': 'Membership',
  'Loan Payment': 'Loan Payment',
  'Pikudon': 'Pikudon'
}

export const SUBSCRIPTION_FREQUENCIES = {
  Once: 'Once',
  Weekly: 'Weekly',
  // 'Bi-Weekly': 'Bi-Weekly',
  Monthly: 'Monthly',
  // 'Bi-Monthly': 'Bi-Monthly',
  // 'Every 3 months': 'Every 3 months',
  // 'Every 6 months': 'Every 6 months',
  Yearly: 'Yearly',
}

export const DEFAULT_SUBSCRIPTION_FEES = {
  membershipFee: (membershipType, membershipAmount) => membershipType === MEMBERSHIP_TYPES.Membership ? (membershipAmount / 24).toFixed(2) : 0,
  processingFee: (gateway, amount) => gateway === AVAILABLE_GATEWAYS.Cardknox ? (amount * 0.03).toFixed(2) : 0,
  declineFee: 20
}
