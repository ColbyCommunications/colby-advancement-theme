module.exports = {
  title: 'Overlay Wide Image',
  status: 'wip',
  context: {
    size: 'medium',
    image: {
      srcset: 'https://via.placeholder.com/880x400',
      src: 'https://via.placeholder.com/400x400',
      alt: 'Test alt',
    },
    subheading: 'Green colby',
    heading: 'Sustainability and Stewardship',
    paragraph: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam congue pulvinar lectus.',
    buttons: [
      {
        url: 'https://welcometruth.com/',
        title: 'Go Green at Colby',
      },
    ],
  },
  variants: [
    {
      name: 'Centered',
      context: {
        align: 'center',
      },
    },
    {
      name: 'Centered Large Dark',
      context: {
        size: 'large',
        align: 'center',
        subheading: 'Ways to Give',
        heading: 'Dare to change the world?',
        paragraph: 'Your gift will help produce leaders to solve the world’s greatest challenges.',
        buttons: [
          {
            url: 'https://welcometruth.com/',
            title: 'Give Now',
          },
        ]
      },
    }
  ]
}