import flow from 'lodash/flow'
import filter from 'lodash/filter'
import React, { Component, PropTypes } from 'react'
import { graphql } from 'react-apollo'
import gql from 'graphql-tag'
import { withLoader } from '../util'
import {
  Table,
  TableHeader,
  TableHeaderColumn,
  TableBody,
  TableRow,
  TableRowColumn
} from 'material-ui/Table'
import IconButton from 'material-ui/IconButton'
import DeleteIcon from 'material-ui/svg-icons/action/delete'
import EditIcon from 'material-ui/svg-icons/editor/mode-edit'
import {grey800, red300} from 'material-ui/styles/colors';

import Dialog from 'material-ui/Dialog';
import FlatButton from 'material-ui/FlatButton';

class TableView extends Component {
  static propTypes = {
    deleteTerminal: PropTypes.func.isRequired
  }

  state = {
    open: false,
    terminalId: false
  };

  handleOpen = (id) => {
    const $this = this;
    return function () {
      $this.setState({open: true, terminalId: id});
    }
  };

  handleClose = () => {
    const $this = this;
    return function () {
      $this.setState({open: false, terminalId: false});
    }
  };

  handleEditClick = () => {
    console.log('Edit operation will be done here.');
  }
  handleDeleteClick = () => {
    const { deleteTerminal } = this.props
    const { terminalId } = this.state
    const $this = this
    return function () {
      deleteTerminal(terminalId, true).then((data) => {
        $this.setState({open: false});
        console.log(data)
      }).catch((error) => {
        console.log(error)
      })
    }
  }

  handleRequestClose = () => {
    const $this = this;
    $this.setState({open: false});
  }

  renderRows = () => {
    const { data: {loading,  terminals } } = this.props
    const iconStyles = {
      width: 20,
      height: 20,
    };

    if (loading) {
      return <div>lade...</div>;
    }
    return terminals.map(({ id, location }, index) => (
      <TableRow key={id}>
        <TableRowColumn>{id}</TableRowColumn>
        <TableRowColumn>{location}</TableRowColumn>
        <TableRowColumn style={{textAlign: 'center'}}>
          <IconButton iconStyle={iconStyles} onClick={this.props.handleOpen(id, location)} title="ändern">
            <EditIcon color={grey800} />
          </IconButton>
          <IconButton iconStyle={iconStyles} onClick={this.handleOpen(id)} title="löschen">
            <DeleteIcon color={red300} />
          </IconButton>
        </TableRowColumn>
      </TableRow>
    ))
  }
  render () {

    const actions = [
      <FlatButton
        label="Abbrechen"
        primary={true}
        onTouchTap={this.handleRequestClose}
      />,
      <FlatButton
        label="Löschen"
        secondary={true}
        onTouchTap={this.handleDeleteClick()}
      />,
    ];
    const customContentStyle = {
      width: '100%',
      maxWidth: 400,
    };

    return (
      <div>
        <Table selectable={false} className='terminals-table-view'>
          <TableHeader adjustForCheckbox={false} displaySelectAll={false}>
            <TableRow>
              <TableHeaderColumn>ID</TableHeaderColumn>
              <TableHeaderColumn>Standort</TableHeaderColumn>
              <TableHeaderColumn style={{textAlign: 'center'}}>Aktion</TableHeaderColumn>
            </TableRow>
          </TableHeader>
          <TableBody displayRowCheckbox={false} stripedRows={true}>
            {this.renderRows()}
          </TableBody>
        </Table>
        <Dialog
          actions={actions}
          modal={false}
          open={this.state.open}
          contentStyle={customContentStyle}
          onRequestClose={this.handleClose}
        >
          Möchten Sie dieses Terminal wirklich löschen?
        </Dialog>
      </div>
    )
  }
}

const TERMINALS_QUERY = gql/* GraphQL */`
  query Terminal {
    terminals {
      id,
      location,
      updatedAt,
      companyId
    }
  }
`;

const DELETE_TERMINAL_MUTATION = gql/* GraphQL */`
  mutation DeleteTerminal($id: ID!, $deleted: Boolean!) {
    deleteTerminal(id: $id, deleted: $deleted) {
      id
    }
  }
`

export default flow([
  withLoader(),
  graphql(TERMINALS_QUERY, {
    options: { pollInterval: 60000 }
  }),
  graphql(DELETE_TERMINAL_MUTATION, {
    props: ({ mutate }) => ({
      deleteTerminal: (terminalId, deleted) => mutate({
        variables: { id: terminalId, deleted: deleted },
        updateQueries: {
          terminals: (prev, { mutationResult }) => {
            // remove deleted terminal from cache
            return {
              ...prev,
              terminals: filter(prev.terminals, (terminal) => (
                terminal.id !== mutationResult.data.deleteTerminal.id
              ))
            }
          }
        },
        refetchQueries: [ 'Terminal']
      })
    })
  })
])(TableView)
