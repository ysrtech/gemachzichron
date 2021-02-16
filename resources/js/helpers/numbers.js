export function numberSuffix(n) {
  return n+=['st','nd','rd'][n%100>>3^1&&n%10]||'th';
}
