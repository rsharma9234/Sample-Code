import { getItems, replaceItems } from 'feathers-hooks-common/lib/utils'

/**
 * Converts a Sequelize Model instance to a JSON Object
 */
export default function toObject (hook) {
  const items = getItems(hook)

  if (Array.isArray(items)) {
    replaceItems(hook, items.map((item) => item.toJSON()))
  } else {
    replaceItems(hook, items.toJSON())
  }

  return hook
}
