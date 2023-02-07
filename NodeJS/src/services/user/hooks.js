import common from 'feathers-hooks-common'
import auth from 'feathers-authentication'
import authLegacy from 'feathers-legacy-authentication-hooks'
import local from 'feathers-authentication-local'

import hooks from '../../hooks'

const restrictToRoles = authLegacy.restrictToRoles({
  roles: [ 'manager' ],
  idField: 'id',
  ownerField: 'id',
  owner: true
})

export default {
  before: {
    all: [
      auth.hooks.authenticate('jwt')
    ],
    find: [
      hooks.auth.hasRoleOrRestrict({
        roles: [ 'manager' ],
        restrict: (hook) => ({ id: hook.params.user.id })
      }),
      hooks.company.queryWithCurrentUsersCompany
    ],
    get: [
      hooks.company.restrictToCompany,
      restrictToRoles
    ],
    create: [
      local.hooks.hashPassword({}),
      hooks.company.associateWithCurrentUsersCompany
    ],
    update: [
      local.hooks.hashPassword({}),
      hooks.company.restrictToCompany
    ],
    patch: [
      local.hooks.hashPassword({}),
      hooks.company.restrictToCompany
    ],
    remove: [
      hooks.company.restrictToCompany
    ]
  },
  after: {
    all: [
      hooks.app.toObject,
      common.remove('password')
    ]
  }
}
