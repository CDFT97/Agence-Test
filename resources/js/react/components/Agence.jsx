import React, { useState, useEffect } from 'react';

export default function AgenceForm({ initialUsersJson, initialFechaInicio, initialFechaFin, csrfToken }) {
  const [users, setUsers] = useState([]);
  const [selectedUserIds, setSelectedUserIds] = useState([]);
  const [allUsersChecked, setAllUsersChecked] = useState(false);
  const [fechaInicio, setFechaInicio] = useState(initialFechaInicio);
  const [fechaFin, setFechaFin] = useState(initialFechaFin);
  const [tipoResultado, setTipoResultado] = useState('report');
  const [modoCalculo, setModoCalculo] = useState('consultor');

  useEffect(() => {
    try {
      if (initialUsersJson) {
        const parsedUsers = JSON.parse(initialUsersJson);
        setUsers(parsedUsers);
      }
    } catch (e) {
      console.error("Error al parsear los datos de usuarios desde Blade:", e);
      setUsers([]);
    }
  }, [initialUsersJson]);

  const handleConsultorCheckboxChange = (event) => {
    const userId = event.target.value;
    const isChecked = event.target.checked;

    setSelectedUserIds(prevSelectedUserIds => {
      if (isChecked) {
        return [...prevSelectedUserIds, userId];
      } else {
        return prevSelectedUserIds.filter(id => id !== userId);
      }
    });
  };

  useEffect(() => {
    if (users.length > 0 && selectedUserIds.length === users.length) {
      setAllUsersChecked(true);
    } else {
      setAllUsersChecked(false);
    }
  }, [selectedUserIds, users]);

  const handleSelectAllChange = (event) => {
    const isChecked = event.target.checked;
    setAllUsersChecked(isChecked);

    if (isChecked) {
      setSelectedUserIds(users.map(user => user.co_usuario));
    } else {
      setSelectedUserIds([]);
    }
  };

  const handleModoCalculoChange = (event) => {
    setModoCalculo(event.target.value);
  };

  const handleTipoResultadoChange = (event) => {
    setTipoResultado(event.target.value);
  };

  const handleFechaInicioChange = (event) => {
    setFechaInicio(event.target.value);
  };

  const handleFechaFinChange = (event) => {
    setFechaFin(event.target.value);
  };

  const selectedUserNames = users
    .filter(user => selectedUserIds.includes(user.co_usuario))
    .map(user => user.no_usuario);

  return (
    <div className="container card shadow mt-4 p-3"> 
      <div className="card-body">
        <div className="btn-group mb-3" role="group" aria-label="Modo de cálculo">
          <input
            type="radio"
            className="btn-check"
            name="options"
            id="option1"
            value="consultor"
            checked={modoCalculo === 'consultor'}
            onChange={handleModoCalculoChange}
            autoComplete="off"
          />
          <label className="btn btn-primary" htmlFor="option1">Por consultor</label>

          <input
            type="radio"
            className="btn-check"
            name="options"
            id="option2"
            value="cliente"
            checked={modoCalculo === 'cliente'}
            onChange={handleModoCalculoChange}
            autoComplete="off"
          />
          <label className="btn btn-outline-primary" htmlFor="option2">Por cliente</label>
        </div>

        <h1 className="mb-4">Calculos Por {modoCalculo === 'consultor' ? 'consultor' : 'cliente'}</h1>

        <form action="/process-form" method="POST">
          <input type="hidden" name="_token" value={csrfToken} />

          <input type="hidden" name="calcMode" value={modoCalculo} />
  
          {selectedUserIds.map(id => (
            <input key={id} type="hidden" name="co_usuario[]" value={id} />
          ))}

          <div className="container py-3 border rounded mb-4"> 
            <h5 className="mb-3">Periodo</h5> 
            <div className="row g-3"> 
              <div className="col-md-6 col-lg-6">
                <label htmlFor="fechaInicio" className="form-label">Fecha inicio :</label> 
                <input
                  name="fechaInicio"
                  type="date"
                  className="form-control"
                  id="fechaInicio"
                  value={fechaInicio}
                  onChange={handleFechaInicioChange}
                />
              </div>

              <div className="col-md-6 col-lg-6">
                <label htmlFor="fechaFin" className="form-label">Fecha fin :</label> 
                <input
                  name="fechaFin"
                  type="date"
                  className="form-control"
                  id="fechaFin"
                  value={fechaFin}
                  onChange={handleFechaFinChange}
                />
              </div>
              
              <div className="col-md-12 col-lg-12 border p-3 rounded">
                <label htmlFor="tipoResultadoSelect" className="form-label mb-2">Seleccione el tipo de resultado a obtener:</label>
                
                <select
                  className="form-select" 
                  name="tipo" 
                  id="tipoResultadoSelect"
                  value={tipoResultado} 
                  onChange={handleTipoResultadoChange}
                >
                  <option value="report">Informe</option>
                  <option value="barchart">Gráfico</option>
                  <option value="pizza">Pizza</option>
                </select>
              </div>
              <div className="col-md-12 col-lg-12"> 
                <div className="w-100"> 
                  <label htmlFor="" className="form-label d-block"> </label> 
                  <button className="btn btn-secondary w-100" type="submit">Consultar</button> 
                </div>
              </div>
            </div>
          </div>

          <h6 className="mb-3">Seleccione los consultores a los cuales desee obtener resultados:</h6> 

          <div className="row">
            <div className="col-sm-12 col-lg-9 mb-3"> 
              <div className="table-responsive"> 
                <table className="table table-striped table-bordered align-middle"> 
                  <thead>
                    <tr>
                      <th className="w-75">Consultores</th>
                      <th className="text-center"> 
                        <span>Seleccionar</span>
                        <input
                          className="form-check-input ms-2"
                          type="checkbox"
                          name="all_usuarios_checkbox"
                          id="all_usuarios"
                          checked={allUsersChecked}
                          onChange={handleSelectAllChange}
                        />
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    {users.map(user => (
                      <tr key={user.co_usuario}>
                        <td className="w-75">{user.no_usuario}</td>
                        <td className="text-center"> 
                          <div className="form-check d-inline-block"> 
                            <input
                              className="form-check-input checkConsultor"
                              type="checkbox"
                              value={user.co_usuario}
                              checked={selectedUserIds.includes(user.co_usuario)}
                              onChange={handleConsultorCheckboxChange}
                              id={`flexCheckDefault-${user.co_usuario}`} 
                            />
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>

            <div className="col-sm-12 col-lg-3">
              <div className="border p-3 rounded"> 
                <h6 className="mt-2 text-center">Seleccionados:</h6>
                <ul className="list-group mt-3">
                  {selectedUserNames.length === 0 ? (
                    <li className="list-group-item text-center text-muted">
                      Ningún consultor seleccionado.
                    </li>
                  ) : (
                    selectedUserNames.map(name => (
                      <li key={name} className="list-group-item text-center">
                        {name}
                      </li>
                    ))
                  )}
                </ul>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  );
}