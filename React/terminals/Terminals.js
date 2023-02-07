import flow from 'lodash/flow'
import _sortBy from 'lodash/sortBy'
import { graphql } from 'react-apollo'

import React, { Component, PropTypes } from 'react'
import TableView from './TableView'
import ContentAdd from 'material-ui/svg-icons/content/add-circle'
import Dialog from 'material-ui/Dialog'
import FlatButton from 'material-ui/FlatButton'
import RaisedButton from 'material-ui/RaisedButton'
import TextField from 'material-ui/TextField'
import gql from 'graphql-tag'

class Terminals extends Component {
  
  static propTypes = {
    createTerminal: PropTypes.func.isRequired,
    updateTerminal: PropTypes.func.isRequired
  };
  
  constructor( props ) {
    super( props );
    
    this.state = {
      open: false,
      submitting: false,
      location: '',
      terminalId: false
    };
  }
  
  handleLocationChange = (event, newtext) => {
    this.setState({ location: newtext })
  }
  
  handleOpen = (id, location) => {
    const $this = this;
    return function () {
      $this.setState({open: true, location: location, terminalId: id});
    }
  };

  handleClose = () => {
    this.setState({open: false});
  };
  
  handleRequestClose = () => {
    const $this = this;
    $this.setState({open: false});
  }
  
  handleSubmit = (event) => {
    event.preventDefault()
    event.stopPropagation()

    this.setState({ submitting: true })
    const { createTerminal, updateTerminal } = this.props
    const { terminalId, location } = this.state
    const $this = this
    
    if(terminalId === false){
      createTerminal({
        location
      }).then((data) => {
        $this.setState({
          open: false,
          submitting: false,
          location: '',
          terminalId: false
        })
        return data
      }).catch((error) => {
        console.log(error)
      })
    }
    else{
      updateTerminal(terminalId, location).then((data) => {
        $this.setState({
          open: false,
          submitting: false,
          location: '',
          terminalId: false
        })
        return data
      }).catch((error) => {
        console.log(error)
      })
    }
  }
  
  render () {
    const iconStyles = {
      width: 48,
      height: 48,
    };
    
    const buttonStyles = {
      minWidth: 48,
      minHeight: 48,
      borderRadius: 50,
    };
    
    const iconPosStyles = {
      marginTop : -50,
      marginRight : -50
    };
    
    const customContentStyle = {
      width: '100%',
      maxWidth: 400,
    };
    
    return (
      <main className='page'>
        <h1 className="heading">
          Übersicht
        </h1>
        <div className='terminals'>
            <div className="fr" style={iconPosStyles}>
              <FlatButton
                icon={<ContentAdd color="#BE1F25" style={iconStyles}/>}
                style={buttonStyles}
                onTouchTap={this.handleOpen(false, '')}
              />
              
            </div>
            <div className="clear"></div>
            <TableView handleOpen={this.handleOpen.bind(this)}/>
            <Dialog
              modal={false}
              open={this.state.open}
              contentStyle={customContentStyle}
              onRequestClose={this.handleClose}
            >
              <form className="" onSubmit={this.handleSubmit}>
                <div className='field'>
                  <TextField
                    fullWidth
                    floatingLabelText='Standort'
                    type='text'
                    value={this.state.location}
                    onChange={this.handleLocationChange}
                  />
                </div>
                
                <div  style={{ marginTop: '2em' }}>
                <RaisedButton className="fr close" default label='SCHLIESSEN' onClick={this.handleRequestClose}/>
                <RaisedButton className="fr" primary label='SPEICHERN' type='submit' disabled={this.submitting}/>
                  <div className="clear"></div>
                </div>
                
              </form>
            </Dialog>
        </div>
      </main>
    )
  }
}

const CREATE_TERMINAL_MUTATION = gql/* GraphQL */`
  mutation createTerminal($location: String!) {
    createTerminal(location: $location) {
      location
    }
  }
`

const UPDATE_TERMINAL_MUTATION = gql/* GraphQL */`
  mutation updateTerminal($id: ID!, $location: String!) {
    updateTerminal(id: $id, location: $location) {
      location
    }
  }
`

export default flow([
  graphql(CREATE_TERMINAL_MUTATION, {
    props: ({ ownProps, mutate }) => ({
      createTerminal: ({location}) => mutate({
        variables: { location },
        updateQueries: {
          terminals: (prev, { mutationResult }) => {
            const newTerminal = mutationResult.data.createTerminal
            const terminals = [ ...prev.terminals, newTerminal]
            
            return {
              ...prev,
              // update existing terminals with new entry
              terminals: _sortBy(terminals, [(t) => t.id])
            }
          }
        },
        refetchQueries: [ 'Terminal']
      })
    })
  }),
  graphql(UPDATE_TERMINAL_MUTATION, {
    props: ({ mutate }) => ({
      updateTerminal: (terminalId, location) => mutate({
        variables: { id: terminalId, location},
        updateQueries: {
          terminals: ({ terminals, ...updated }, { mutationResult }) => {
            return {
              ...updated,
              terminals: _sortBy(terminals, [(t) => t.id])
            }
          }
        },
        refetchQueries: [ 'Terminal']
      })
    })
  })
])(Terminals)
// export default Terminals