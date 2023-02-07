import ApolloClient, { createNetworkInterface } from 'apollo-client'
import { GRAPHQL_URL } from './constants'

import auth from './auth/auth'

const networkInterface = createNetworkInterface({ uri: GRAPHQL_URL })

const apollo = new ApolloClient({
  networkInterface,
  dataIdFromObject: (result) => {
    if (result.id && result.__typename) {
      return result.__typename + result.id
    }

    return null
  }
})

networkInterface.use([{
  applyMiddleware (req, next) {
    if (!req.options.headers) {
      req.options.headers = {} // create Headers object if needed.
    }

    req.options.headers['authorization'] = auth.retrieveToken()
      ? auth.retrieveToken()
      : null
    next()
  }
}])

networkInterface.useAfter([{
  applyAfterware ({ response }, next) {
    // check for http 401 = not authorized
    if (response.status === 401) {
      auth.signOut()
    }

    next()
  }
}])

export default apollo
