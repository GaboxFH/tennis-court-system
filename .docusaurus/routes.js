
import React from 'react';
import ComponentCreator from '@docusaurus/ComponentCreator';
export default [
{
  path: '/tennis-court-system/blog',
  component: ComponentCreator('/tennis-court-system/blog','e3e'),
  exact: true,
},
{
  path: '/tennis-court-system/blog/hello-world',
  component: ComponentCreator('/tennis-court-system/blog/hello-world','d0f'),
  exact: true,
},
{
  path: '/tennis-court-system/blog/hola',
  component: ComponentCreator('/tennis-court-system/blog/hola','263'),
  exact: true,
},
{
  path: '/tennis-court-system/blog/tags',
  component: ComponentCreator('/tennis-court-system/blog/tags','b81'),
  exact: true,
},
{
  path: '/tennis-court-system/blog/tags/docusaurus',
  component: ComponentCreator('/tennis-court-system/blog/tags/docusaurus','47c'),
  exact: true,
},
{
  path: '/tennis-court-system/blog/tags/facebook',
  component: ComponentCreator('/tennis-court-system/blog/tags/facebook','424'),
  exact: true,
},
{
  path: '/tennis-court-system/blog/tags/hello',
  component: ComponentCreator('/tennis-court-system/blog/tags/hello','e49'),
  exact: true,
},
{
  path: '/tennis-court-system/blog/tags/hola',
  component: ComponentCreator('/tennis-court-system/blog/tags/hola','f02'),
  exact: true,
},
{
  path: '/tennis-court-system/blog/welcome',
  component: ComponentCreator('/tennis-court-system/blog/welcome','15f'),
  exact: true,
},
{
  path: '/tennis-court-system/',
  component: ComponentCreator('/tennis-court-system/','a65'),
  
  routes: [
{
  path: '/tennis-court-system/',
  component: ComponentCreator('/tennis-court-system/','5b2'),
  exact: true,
},
{
  path: '/tennis-court-system/api',
  component: ComponentCreator('/tennis-court-system/api','613'),
  exact: true,
},
{
  path: '/tennis-court-system/doc1',
  component: ComponentCreator('/tennis-court-system/doc1','141'),
  exact: true,
},
{
  path: '/tennis-court-system/fdreservations',
  component: ComponentCreator('/tennis-court-system/fdreservations','e7d'),
  exact: true,
},
{
  path: '/tennis-court-system/github',
  component: ComponentCreator('/tennis-court-system/github','255'),
  exact: true,
},
{
  path: '/tennis-court-system/mdx',
  component: ComponentCreator('/tennis-court-system/mdx','e20'),
  exact: true,
},
{
  path: '/tennis-court-system/userreservations',
  component: ComponentCreator('/tennis-court-system/userreservations','087'),
  exact: true,
},
]
},
{
  path: '*',
  component: ComponentCreator('*')
}
];
