import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

async function main() {
  const user1 = await prisma.users.create({
    data: {
      name: 'John Doe',
      email: 'john@example.com',
      password: 'password123',
      posts: {
        create: [
          {
            title: 'First Post',
            content: 'This is the content of the first post',
          },
          {
            title: 'Second Post',
            content: 'This is the content of the second post',
          },
        ],
      },
    },
  });

  const user2 = await prisma.users.create({
    data: {
      name: 'Jane Doe',
      email: 'jane@example.com',
      password: 'password456',
      posts: {
        create: [
          {
            title: 'Another Post',
            content: 'This is the content of another post',
          },
        ],
      },
    },
  });

  console.log({ user1, user2 });
}

main()
  .then(async () => {
    await prisma.$disconnect();
  })
  .catch(async e => {
    console.error(e);
    await prisma.$disconnect();
    process.exit(1);
  });
