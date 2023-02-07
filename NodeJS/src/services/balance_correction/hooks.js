import auth from 'feathers-authentication'

import hooks from '../../hooks'

export default {
  before: {
    all: [
      auth.hooks.authenticate('jwt')
    ],
    find: [
      hooks.company.queryWithCurrentUsersCompany
    ],
    get: [
      hooks.company.restrictToCompany
    ],
    create: [
      hooks.company.associateWithCurrentUsersCompany
    ],
    update: [
      hooks.company.restrictToCompany
    ],
    patch: [
      hooks.company.restrictToCompany
    ],
    remove: [
      hooks.company.restrictToCompany
    ]
  },
  after: {
    all: [
      hooks.app.toObject
    ]
  }
}
