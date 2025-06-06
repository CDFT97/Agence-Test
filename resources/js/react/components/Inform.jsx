import React from 'react';

const Inform = ({ titulo, data }) => {
  // Función idéntica a precioFormato de PHP
  const precioFormato = (number) => {
    const fixedNum = parseFloat(number).toFixed(2);
    const parts = fixedNum.toString().split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return 'R$ ' + parts.join(',');
  };

  return (
    <div className="container card shadow">
      <div className="card-body">
        <a className="btn btn-primary float-end" href="/">Volver a consultar</a>
        <h1 style={{ fontSize: '1.5rem' }} className="mt-5 text-center">{titulo}</h1>

        {Object.entries(data).map(([co_usuario, datos]) => {
          let totalReceitaLiquida = 0;
          let totalCustoFixo = 0;
          let totalComision = 0;
          let totalLucro = 0;

          return (
            <div key={co_usuario} className="table-responsive">
              <table className="table table-striped mt-4">
                <thead>
                  <tr className="table-dark">
                    <th colSpan="5">
                      <h3 className="ml-2">{co_usuario}</h3>
                    </th>
                  </tr>
                  <tr>
                    <th>Periodo</th>
                    <th>Ganancia neta</th>
                    <th>Costo fijo</th>
                    <th>Comisión</th>
                    <th>Lucro</th>
                  </tr>
                </thead>
                <tbody>
                  {Object.entries(datos).map(([mes, valores]) => {
                    const lucro = valores.gananciasNetas - (valores.costo_fijo + valores.comision);
                    totalReceitaLiquida += valores.gananciasNetas;
                    totalCustoFixo += valores.costo_fijo;
                    totalComision += valores.comision;
                    totalLucro += lucro;

                    return (
                      <tr key={mes}>
                        <th>{mes}</th>
                        <td>{precioFormato(valores.gananciasNetas)}</td>
                        <td>{precioFormato(valores.costo_fijo)}</td>
                        <td>{precioFormato(valores.comision)}</td>
                        <td>{precioFormato(lucro)}</td>
                      </tr>
                    );
                  })}
                  <tr className="table-dark">
                    <td>Saldo</td>
                    <td>{precioFormato(totalReceitaLiquida)}</td>
                    <td>{precioFormato(totalCustoFixo)}</td>
                    <td>{precioFormato(totalComision)}</td>
                    <td>{precioFormato(totalLucro)}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          );
        })}
      </div>
    </div>
  );
};

export default Inform;