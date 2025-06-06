import React from 'react';
import Highcharts from 'highcharts';
import HighchartsReact from 'highcharts-react-official';

const Pizza = ({ title, format }) => {
  const options = {
    chart: {
      type: 'pie',
      backgroundColor: 'transparent'
    },
    title: {
      text: title,
      style: {
        color: '#333',
        fontWeight: 'bold'
      }
    },
    tooltip: {
      valueSuffix: '%',
      formatter: function() {
        return `<b>% ${this.y.toFixed(2)}</b>`;
      }
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
        cursor: 'pointer',
        dataLabels: [
          {
            enabled: true,
            distance: 20,
            style: {
              fontWeight: 'bold',
              color: 'white'
            }
          },
          {
            enabled: true,
            distance: -40,
            format: '{point.percentage:.1f}%',
            style: {
              fontSize: '1.2em',
              textOutline: 'none',
              opacity: 0.7,
              color: 'white'
            },
            filter: {
              operator: '>',
              property: 'percentage',
              value: 10
            }
          }
        ],
        showInLegend: true
      }
    },
    series: [{
      name: 'Percentage',
      colorByPoint: true,
      data: format,
      size: '80%',
      innerSize: '50%'
    }],
    responsive: {
      rules: [{
        condition: {
          maxWidth: 500
        },
        chartOptions: {
          plotOptions: {
            pie: {
              size: '100%',
              innerSize: '30%'
            }
          }
        }
      }]
    }
  };

  return (
    <div className="container card mt-5 shadow">
      <div className="card-body">
        <a className="btn btn-primary float-end" href="/">Volver a consultar</a>
        <h1>Pizza</h1>
        <HighchartsReact
          highcharts={Highcharts}
          options={options}
          containerProps={{
            style: {
              height: '500px',
              minWidth: '100%',
              marginTop: '20px'
            }
          }}
        />
      </div>
    </div>
  );
};


export default Pizza;