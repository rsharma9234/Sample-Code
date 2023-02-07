import _ from 'lodash'
import { checkContext } from 'feathers-hooks-common/lib/utils'
import errors from 'feathers-errors'

const defaults = {
  fieldName: 'roles'
}

export default function (options = {}) {
  if (!options.roles || !options.roles.length) {
    throw new Error('Argument Exception: options.roles must be provided')
  }

  return function (hook) {
    checkContext(hook, 'before', [ 'find' ])

    if (!hook.params.provider) {
      return hook
    }

    options = Object.assign({}, defaults, options)
    const roles = hook.params.user[options.fieldName]
    let authorized = false

    authorized = roles.some((role) => options.roles.indexOf(role) !== -1)

    if (!authorized) {
      if (options.restrict) {
        hook.params.query = _.merge(hook.params.query, options.restrict(hook))
      } else {
        throw new errors.Forbidden('You do not have valid permissions to access this')
      }
    }

    return hook
  }
}
