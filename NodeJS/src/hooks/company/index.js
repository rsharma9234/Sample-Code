import hooks from 'feathers-legacy-authentication-hooks'

export const queryWithCurrentUsersCompany = hooks.queryWithCurrentUser({
  idField: 'companyId',
  as: 'companyId'
})

export const associateWithCurrentUsersCompany = hooks.associateCurrentUser({
  idField: 'companyId',
  as: 'companyId'
})

export const restrictToCompany = hooks.restrictToOwner({
  idField: 'companyId',
  ownerField: 'companyId'
})

export function createCompanyForNewUser (hook) {
  const { app, result } = hook

  // only for external requests!
  if (hook.params.provider) {
    if (!result.companyId) {
      const companyService = app.service('companies')
      const userService = app.service('users')

      return companyService
        .create({ name: 'Mein Unternehmen', ownerId: result.id })
        .then((company) => {
          result.companyId = company.id
          // update user with new companyId
          return userService.patch(result.id, { companyId: company.id })
        }).then(() => Promise.resolve(hook))
    }
  }

  return hook
}
