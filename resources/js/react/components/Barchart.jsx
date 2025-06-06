import React from 'react';
import Highcharts from 'highcharts';
import HighchartsReact from 'highcharts-react-official';

const Barchart = ({ title, format, mesesName }) => {

  const options = {
    chart: {
      type: 'column'
    },
    title: {
      text: title,
      align: 'left'
    },
    xAxis: {
      categories: mesesName,
      crosshair: true
    },
    yAxis: {
      title: {
        text: ''
      },
      max: 32000
    },
    tooltip: {
      shared: true,
      valueSuffix: '',
      formatter: function () {
        return '<b>' + this.y.toLocaleString('pt-BR', {
          style: 'currency',
          currency: 'BRL'
        }) + '</b>';
      }
    },
    plotOptions: {
      column: {
        borderRadius: '25%',
        pointPadding: 0.2,
        borderWidth: 0
      },
      series: {
        animation: {
          duration: 2000
        }
      }
    },
    series: format
  };

  return (
    <div className="container mt-5 card shadow">
      <div className="card-body">
        <a className="btn btn-primary float-end" href="/">Volver a consultar</a>
        <h1>Gráfico</h1>
        <HighchartsReact
          highcharts={Highcharts}
          options={options}
          containerProps={{ style: { height: '500px', width: '100%' } }}
        />
      </div>
    </div>
  );
};

export default Barchart;