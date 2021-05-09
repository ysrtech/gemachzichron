export function date(value, options = {year: 'numeric', month: 'long', day: 'numeric'}) {
  return (new Date(value)).toLocaleString([], options)
}
