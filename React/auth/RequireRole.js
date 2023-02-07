import _flow from 'lodash/flow'
import _indexOf from 'lodash/indexOf'
import gql from 'graphql-tag'
import React, { Component, PropTypes } from 'react'
import { graphql } from 'react-apollo'

import Auth from './auth'
import { withLoader } from '../util'

class RequireRole extends Component {
  static propTypes = {
    roles: PropTypes.string.isRequired
  }

  render () {
    const { children, roles, data: { user } } = this.props
    const authorized = _indexOf(user.roles, roles) !== -1

    if (!authorized) {
      return null
    }

    return (
      <div>{children}</div>
    )
  }
}

const CURRENT_USER_QUERY = gql/* GraphQL */`
  query currentUser($id: ID!) {
    user(id: $id) {
      id
      roles
    }
  }
`

export default _flow([
  withLoader(),
  graphql(CURRENT_USER_QUERY, {
    options: () => ({
      variables: { id: Auth.getCurrentUserId() }
    })
  })
])(RequireRole)
